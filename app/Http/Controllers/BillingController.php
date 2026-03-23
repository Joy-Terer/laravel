<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\AuditLog;
use App\Services\MpesaService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function __construct(
        private MpesaService  $mpesa,
        private PayPalService $paypal
    ) {}

    // ── Plans page ─────────────────────────────────────────────────
    public function plans()
    {
        $plans        = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $chama        = Auth::user()->chama;
        $currentPlan  = $chama->plan;
        $subscription = $chama->activeSubscription();

        return view('billing.plans', compact('plans', 'chama', 'currentPlan', 'subscription'));
    }

    // ── Select / upgrade plan ──────────────────────────────────────
    public function selectPlan(Request $request, Plan $plan)
    {
        $chama = Auth::user()->chama;

        // Free plan — no payment needed
        if ($plan->isFree()) {
            $this->assignPlan($chama, $plan, 'free');

            return redirect()->route('dashboard')
                ->with('success', 'You are now on the Free plan.');
        }

        // Paid plan — go to checkout
        return redirect()->route('billing.checkout', $plan->slug);
    }

    // ── Checkout page ──────────────────────────────────────────────
    public function checkout(Plan $plan)
    {
        $chama = Auth::user()->chama;

        return view('billing.checkout', compact('plan', 'chama'));
    }

    // ── Process M-Pesa payment ─────────────────────────────────────
    public function payMpesa(Request $request, Plan $plan)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $chama  = Auth::user()->chama;
        $phone  = $this->formatPhone($request->phone);
        $amount = (int) $plan->price_kes;

        $result = $this->mpesa->stkPush(
            phone:  $phone,
            amount: $amount,
            ref:    'SmartChama-Sub',
            desc:   "SmartChama {$plan->name} Plan"
        );

        if ($result['success']) {
            // Create pending invoice
            $invoice = SubscriptionInvoice::create([
                'subscription_id' => $this->getOrCreateSubscription($chama, $plan)->id,
                'chama_id'        => $chama->id,
                'invoice_number'  => SubscriptionInvoice::generateInvoiceNumber(),
                'amount_kes'      => $plan->price_kes,
                'amount_usd'      => $plan->price_usd,
                'currency'        => 'KES',
                'payment_method'  => 'mpesa',
                'transaction_code'=> $result['checkout_request_id'],
                'status'          => 'pending',
                'due_date'        => now()->addDays(1),
            ]);

            session(['pending_invoice_id' => $invoice->id]);

            return redirect()->route('billing.pending')
                ->with('success', 'M-Pesa payment prompt sent to your phone. Enter your PIN to confirm.');
        }

        return back()->withErrors(['payment' => 'M-Pesa request failed. Please try again.']);
    }

    // ── M-Pesa callback ────────────────────────────────────────────
    public function mpesaCallback(Request $request)
    {
        $body       = $request->input('Body.stkCallback');
        $resultCode = $body['ResultCode'] ?? 1;
        $checkoutId = $body['CheckoutRequestID'] ?? null;

        $invoice = SubscriptionInvoice::where('transaction_code', $checkoutId)->first();
        if (!$invoice) return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        if ($resultCode === 0) {
            $meta    = collect($body['CallbackMetadata']['Item'] ?? []);
            $receipt = $meta->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;

            $invoice->update([
                'status'           => 'paid',
                'transaction_code' => $receipt,
                'paid_at'          => now(),
            ]);

            // Activate subscription
            $this->activateSubscription($invoice->subscription);

            AuditLog::log(
                'subscription.paid',
                "{$invoice->chama->name} paid KES {$invoice->amount_kes} for {$invoice->subscription->plan->name} plan via M-Pesa."
            );
        } else {
            $invoice->update(['status' => 'failed']);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // ── Process PayPal payment ─────────────────────────────────────
    public function payPaypal(Request $request, Plan $plan)
    {
        $chama = Auth::user()->chama;

        $order = $this->paypal->createOrder(
            $plan->price_kes,
            Auth::id(),
            $chama->id
        );

        if ($order['success']) {
            session(['paypal_plan_id'   => $plan->id]);
            session(['paypal_chama_id'  => $chama->id]);
            session(['paypal_order_id'  => $order['order_id']]);

            return redirect($order['approval_url']);
        }

        return back()->withErrors(['payment' => 'PayPal request failed. Please try again.']);
    }

    // ── PayPal success ─────────────────────────────────────────────
    public function paypalSuccess(Request $request)
    {
        $planId  = session('paypal_plan_id');
        $chamaId = session('paypal_chama_id');
        $orderId = $request->get('token');

        $plan    = Plan::findOrFail($planId);
        $chama   = \App\Models\Chama::findOrFail($chamaId);
        $capture = $this->paypal->captureOrder($orderId);

        if ($capture['success']) {
            $subscription = $this->getOrCreateSubscription($chama, $plan);

            SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'chama_id'        => $chama->id,
                'invoice_number'  => SubscriptionInvoice::generateInvoiceNumber(),
                'amount_kes'      => $plan->price_kes,
                'amount_usd'      => $plan->price_usd,
                'currency'        => 'USD',
                'payment_method'  => 'paypal',
                'transaction_code'=> $capture['transaction_id'],
                'status'          => 'paid',
                'paid_at'         => now(),
            ]);

            $this->activateSubscription($subscription);

            session()->forget(['paypal_plan_id', 'paypal_chama_id', 'paypal_order_id']);

            return redirect()->route('billing.plans')
                ->with('success', "Payment confirmed! You are now on the {$plan->name} plan.");
        }

        return redirect()->route('billing.plans')
            ->withErrors(['payment' => 'PayPal payment could not be captured. Please try again.']);
    }

    // ── PayPal cancel ──────────────────────────────────────────────
    public function paypalCancel()
    {
        session()->forget(['paypal_plan_id', 'paypal_chama_id', 'paypal_order_id']);
        return redirect()->route('billing.plans')
            ->with('warning', 'PayPal payment was cancelled.');
    }

    // ── Pending payment page ───────────────────────────────────────
    public function pending()
    {
        return view('billing.pending');
    }

    // ── Billing history ────────────────────────────────────────────
    public function history()
    {
        $chama    = Auth::user()->chama;
        $invoices = SubscriptionInvoice::where('chama_id', $chama->id)
            ->with('subscription.plan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('billing.history', compact('invoices', 'chama'));
    }

    // ── Cancel subscription ────────────────────────────────────────
    public function cancel(Request $request)
    {
        $chama        = Auth::user()->chama;
        $subscription = $chama->activeSubscription();

        if ($subscription) {
            $subscription->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'auto_renew'   => false,
            ]);

            AuditLog::log(
                'subscription.cancelled',
                Auth::user()->name . " cancelled the {$subscription->plan->name} plan for {$chama->name}."
            );
        }

        return redirect()->route('billing.plans')
            ->with('success', 'Subscription cancelled. You will retain access until the end of your billing period.');
    }

    // ── Helpers ────────────────────────────────────────────────────
    private function getOrCreateSubscription($chama, Plan $plan): Subscription
    {
        return Subscription::firstOrCreate(
            ['chama_id' => $chama->id, 'status' => 'pending'],
            [
                'plan_id'    => $plan->id,
                'starts_at'  => now(),
                'ends_at'    => now()->addMonth(),
                'auto_renew' => true,
            ]
        );
    }

    private function activateSubscription(Subscription $subscription): void
    {
        $subscription->update([
            'status'   => 'active',
            'starts_at'=> now(),
            'ends_at'  => now()->addMonth(),
        ]);

        $this->assignPlan($subscription->chama, $subscription->plan, $subscription->payment_method ?? 'mpesa');
    }

    private function assignPlan($chama, Plan $plan, string $method): void
    {
        $chama->update(['plan_id' => $plan->id]);
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '254' . substr($phone, 1);
        if (str_starts_with($phone, '+')) $phone = ltrim($phone, '+');
        return $phone;
    }
}