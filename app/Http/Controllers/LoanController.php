<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    // ── List ──────────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        $loans = in_array($user->role, ['admin', 'treasurer'])
            ? Loan::where('chama_id', $user->chama_id)->with('user')->orderBy('created_at', 'desc')->get()
            : Loan::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('loans.index', compact('loans'));
    }

    // ── Application form ──────────────────────────────────────────
    public function apply()
    {
        $user               = Auth::user();
        $totalContributions = Contribution::where('user_id', $user->id)
            ->where('status', 'completed')->sum('amount');
        $maxLoan = $totalContributions * 2;

        if ($user->hasActiveLoan()) {
            return redirect()->route('loans.index')
                ->with('warning', 'You already have an active loan. Please repay it before applying for a new one.');
        }

        return view('loans.apply', compact('totalContributions', 'maxLoan'));
    }

    // ── Submit application ────────────────────────────────────────
    public function store(LoanRequest $request)
    {
        $user  = Auth::user();
        $chama = $user->chama;

        Loan::create([
            'user_id'          => $user->id,
            'chama_id'         => $chama->id,
            'amount'           => $request->amount,
            'balance'          => $request->amount,
            'status'           => 'pending',
            'purpose'          => $request->purpose,
            'repayment_period' => $request->repayment_period,
        ]);

        // Notify member + admins
        $this->notifications->loanApplicationReceived($user, $request->amount);

        AuditLog::log('loan.applied',
            "{$user->name} applied for a loan of KES " . number_format($request->amount, 0) . "."
        );

        return redirect()->route('loans.index')
            ->with('success', 'Loan application submitted. You will be notified once reviewed.');
    }

    // ── Approve ───────────────────────────────────────────────────
    public function approve(Request $request, Loan $loan)
    {
        $this->authorize('manage', $loan);

        if ($loan->status !== 'pending') {
            return back()->withErrors(['loan' => 'This loan has already been processed.']);
        }

        $dueDate = now()->addMonths($loan->repayment_period)->toDateString();

        $loan->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'due_date'    => $dueDate,
        ]);

        $loan->chama->decrement('balance', $loan->amount);

        // Notify member
        $this->notifications->loanApproved(
            $loan->user,
            $loan->amount,
            now()->addMonths($loan->repayment_period)->format('d M Y')
        );

        AuditLog::log('loan.approved',
            Auth::user()->name . " approved loan of KES " . number_format($loan->amount, 0) . " for {$loan->user->name}."
        );

        return back()->with('success', "Loan approved for {$loan->user->name}.");
    }

    // ── Decline ───────────────────────────────────────────────────
    public function decline(Request $request, Loan $loan)
    {
        $this->authorize('manage', $loan);

        $request->validate([
            'decline_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $request->decline_reason ?? 'Declined by administrator.';

        $loan->update([
            'status'         => 'declined',
            'decline_reason' => $reason,
        ]);

        // Notify member
        $this->notifications->loanDeclined($loan->user, $loan->amount, $reason);

        AuditLog::log('loan.declined',
            Auth::user()->name . " declined loan request for {$loan->user->name}."
        );

        return back()->with('success', "Loan request for {$loan->user->name} has been declined.");
    }

    // ── Repay ─────────────────────────────────────────────────────
    public function repay(Request $request, Loan $loan)
    {
        $request->validate([
            'amount'         => ['required', 'numeric', 'min:1', 'max:' . $loan->balance],
            'payment_method' => ['required', 'in:mpesa,paypal,wave,cash'],
        ]);

        if ($loan->user_id !== Auth::id()) {
            abort(403, 'Unauthorised action.');
        }

        if (!in_array($loan->status, ['approved', 'active'])) {
            return back()->withErrors(['loan' => 'This loan cannot be repaid.']);
        }

        $newBalance = max($loan->balance - $request->amount, 0);

        Repayment::create([
            'loan_id'           => $loan->id,
            'user_id'           => Auth::id(),
            'amount_paid'       => $request->amount,
            'balance_remaining' => $newBalance,
            'payment_method'    => $request->payment_method,
            'payment_date'      => now()->toDateString(),
        ]);

        $loan->update([
            'balance' => $newBalance,
            'status'  => $newBalance <= 0 ? 'repaid' : $loan->status,
        ]);

        $loan->chama->increment('balance', $request->amount);

        AuditLog::log('loan.repayment',
            Auth::user()->name . " repaid KES " . number_format($request->amount, 0) . " on loan #{$loan->id}. Remaining: KES " . number_format($newBalance, 0)
        );

        $msg = $newBalance <= 0
            ? 'Congratulations! Your loan is fully repaid.'
            : 'Repayment of KES ' . number_format($request->amount, 0) . ' recorded. Remaining: KES ' . number_format($newBalance, 0);

        return back()->with('success', $msg);
    }
}