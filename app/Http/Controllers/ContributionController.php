<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributionRequest;
use App\Models\Contribution;
use App\Models\AuditLog;
use App\Services\MpesaService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContributionController extends Controller
{
    public function __construct(
        private MpesaService  $mpesa,
        private PayPalService $paypal
    ) {}

    // ── List ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = in_array($user->role, ['admin', 'treasurer'])
            ? Contribution::where('chama_id', $user->chama_id)->with('user')
            : Contribution::where('user_id', $user->id);

        if ($request->filled('from'))   $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))     $query->whereDate('created_at', '<=', $request->to);
        if ($request->filled('method')) $query->where('payment_method', $request->method);

        $contributions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('contributions.index', compact('contributions'));
    }

    // ── Create form ───────────────────────────────────────────────
    public function create()
    {
        $chama = Auth::user()->chama;
        return view('contributions.create', compact('chama'));
    }

    // ── Store ─────────────────────────────────────────────────────
    public function store(ContributionRequest $request)
    {
        $user  = Auth::user();
        $chama = $user->chama;

        return match($request->payment_method) {
            'mpesa'  => $this->processMpesa($request, $user, $chama),
            'paypal' => $this->processPayPal($request, $user, $chama),
            'wave'   => $this->processWave($request, $user, $chama),
            'cash'   => $this->processCash($request, $user, $chama),
            default  => back()->withErrors(['payment_method' => 'Invalid payment method.']),
        };
    }

    // ── M-Pesa ────────────────────────────────────────────────────
    private function processMpesa($request, $user, $chama)
    {
        $phone  = $this->formatPhone($request->phone ?? $user->phone);
        $result = $this->mpesa->stkPush(
            phone:  $phone,
            amount: (int) $request->amount,
            ref:    'SmartChama',
            desc:   'Chama Contribution'
        );

        if ($result['success']) {
            Contribution::create([
                'user_id'         => $user->id,
                'chama_id'        => $chama->id,
                'amount'          => $request->amount,
                'payment_method'  => 'mpesa',
                'transaction_ref' => $result['checkout_request_id'],
                'status'          => 'pending',
                'notes'           => $request->notes,
            ]);

            return redirect()->route('contributions.index')
                ->with('success', 'M-Pesa prompt sent to your phone. Enter your PIN to confirm.');
        }

        return back()->withErrors(['payment' => 'M-Pesa request failed. Please try again.']);
    }

    // ── M-Pesa Callback (Safaricom posts here) ────────────────────
    public function mpesaCallback(Request $request)
    {
        $body       = $request->input('Body.stkCallback');
        $resultCode = $body['ResultCode'] ?? 1;
        $checkoutId = $body['CheckoutRequestID'] ?? null;

        $contribution = Contribution::where('transaction_ref', $checkoutId)->first();
        if (!$contribution) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($resultCode === 0) {
            $meta    = collect($body['CallbackMetadata']['Item'] ?? []);
            $receipt = $meta->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
            $amount  = $meta->firstWhere('Name', 'Amount')['Value'] ?? $contribution->amount;

            $contribution->update([
                'status'           => 'completed',
                'transaction_code' => $receipt,
                'amount'           => $amount,
            ]);

            $contribution->chama->increment('balance', $amount);

            AuditLog::create([
                'user_id'     => $contribution->user_id,
                'action'      => 'contribution.completed',
                'description' => "{$contribution->user->name} contributed KES " . number_format($amount, 0) . " via M-Pesa. Ref: {$receipt}",
                'ip_address'  => $request->ip(),
            ]);
        } else {
            $contribution->update(['status' => 'failed']);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // ── PayPal ────────────────────────────────────────────────────
    private function processPayPal($request, $user, $chama)
    {
        $order = $this->paypal->createOrder($request->amount, $user->id, $chama->id);

        if ($order['success']) {
            session(['paypal_contribution' => [
                'user_id'  => $user->id,
                'chama_id' => $chama->id,
                'amount'   => $request->amount,
                'order_id' => $order['order_id'],
                'notes'    => $request->notes,
            ]]);

            return redirect($order['approval_url']);
        }

        return back()->withErrors(['payment' => 'PayPal order creation failed. Please try again.']);
    }

    public function paypalSuccess(Request $request)
    {
        $data    = session('paypal_contribution');
        $orderId = $request->get('token');
        $capture = $this->paypal->captureOrder($orderId);

        if ($capture['success']) {
            $contribution = Contribution::create([
                'user_id'          => $data['user_id'],
                'chama_id'         => $data['chama_id'],
                'amount'           => $data['amount'],
                'payment_method'   => 'paypal',
                'transaction_code' => $capture['transaction_id'],
                'status'           => 'completed',
                'notes'            => $data['notes'] ?? null,
            ]);

            $contribution->chama->increment('balance', $data['amount']);

            AuditLog::create([
                'user_id'     => $data['user_id'],
                'action'      => 'contribution.completed',
                'description' => "Contribution of KES " . number_format($data['amount'], 0) . " made via PayPal.",
                'ip_address'  => request()->ip(),
            ]);

            session()->forget('paypal_contribution');

            return redirect()->route('contributions.index')
                ->with('success', 'PayPal payment confirmed! KES ' . number_format($data['amount'], 0) . ' recorded.');
        }

        return redirect()->route('contributions.create')
            ->withErrors(['payment' => 'PayPal payment could not be captured. Please try again.']);
    }

    public function paypalCancel()
    {
        session()->forget('paypal_contribution');
        return redirect()->route('contributions.create')
            ->with('warning', 'PayPal payment was cancelled.');
    }

    // ── Wave ──────────────────────────────────────────────────────
    private function processWave($request, $user, $chama)
    {
        Contribution::create([
            'user_id'          => $user->id,
            'chama_id'         => $chama->id,
            'amount'           => $request->amount,
            'payment_method'   => 'wave',
            'transaction_code' => 'WAVE-' . strtoupper(Str::random(10)),
            'status'           => 'pending',
            'notes'            => $request->notes,
        ]);

        return redirect()->route('contributions.index')
            ->with('success', 'Wave payment initiated. Your contribution will be confirmed shortly.');
    }

    // ── Cash (Treasurer/Admin only) ───────────────────────────────
    private function processCash($request, $user, $chama)
    {
        if (!in_array($user->role, ['treasurer', 'admin'])) {
            return back()->withErrors(['payment' => 'Only the Treasurer can record cash contributions.']);
        }

        Contribution::create([
            'user_id'          => $user->id,
            'chama_id'         => $chama->id,
            'amount'           => $request->amount,
            'payment_method'   => 'cash',
            'transaction_code' => 'CASH-' . strtoupper(Str::random(8)),
            'status'           => 'completed',
            'notes'            => $request->notes,
        ]);

        $chama->increment('balance', $request->amount);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'contribution.completed',
            'description' => "{$user->name} recorded cash contribution of KES " . number_format($request->amount, 0) . ".",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('contributions.index')
            ->with('success', 'Cash contribution of KES ' . number_format($request->amount, 0) . ' recorded.');
    }

    // ── Helper ────────────────────────────────────────────────────
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '254' . substr($phone, 1);
        if (str_starts_with($phone, '+')) $phone = ltrim($phone, '+');
        return $phone;
    }
}