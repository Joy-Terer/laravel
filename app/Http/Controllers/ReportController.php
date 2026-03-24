<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\User;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // ── Default view (current month) ──────────────────────────────
    public function index()
    {
        $chama = Auth::user()->chama;

        $totalContributions = Contribution::where('chama_id', $chama->id)
            ->where('status', 'completed')->sum('amount');

        $totalLoans = Loan::where('chama_id', $chama->id)->sum('amount');

        $totalRepayments = Repayment::whereHas('loan', fn($q) =>
            $q->where('chama_id', $chama->id)
        )->sum('amount_paid');

        $memberBreakdown = $this->buildMemberBreakdown($chama->id);

        return view('reports.index', compact(
            'totalContributions', 'totalLoans',
            'totalRepayments', 'memberBreakdown'
        ));
    }

    // ── Generate for custom date range ────────────────────────────
    public function generate(ReportRequest $request)
    {
        $from  = Carbon::parse($request->from)->startOfDay();
        $to    = Carbon::parse($request->to)->endOfDay();
        $chama = Auth::user()->chama;

        $totalContributions = Contribution::where('chama_id', $chama->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $totalLoans = Loan::where('chama_id', $chama->id)
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $totalRepayments = Repayment::whereHas('loan', fn($q) =>
            $q->where('chama_id', $chama->id)
        )->whereBetween('payment_date', [$from, $to])->sum('amount_paid');

        $memberBreakdown = $this->buildMemberBreakdown($chama->id, $from, $to);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'report.generated',
            'description' => Auth::user()->name . " generated a report for {$from->format('d M Y')} — {$to->format('d M Y')}.",
            'ip_address'  => request()->ip(),
        ]);

        return view('reports.index', compact(
            'totalContributions', 'totalLoans',
            'totalRepayments', 'memberBreakdown', 'from', 'to'
        ));
    }

    // ── PDF download ──────────────────────────────────────────────
    public function pdf()
    {
        $chama   = Auth::user()->chama;
        $from    = Carbon::now()->startOfMonth();
        $to      = Carbon::now()->endOfMonth();
        $members = User::where('chama_id', $chama->id)->where('status', 'active')->get();

        $contributions = Contribution::where('chama_id', $chama->id)
            ->with('user')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        $loans = Loan::where('chama_id', $chama->id)
            ->with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $totalContributions = $contributions->where('status', 'completed')->sum('amount');
        $totalLoans         = $loans->sum('amount');
        $totalRepayments    = Repayment::whereHas('loan', fn($q) =>
            $q->where('chama_id', $chama->id)
        )->whereBetween('payment_date', [$from, $to])->sum('amount_paid');

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'report.pdf.downloaded',
            'description' => Auth::user()->name . " downloaded a PDF financial report.",
            'ip_address'  => request()->ip(),
        ]);

        $pdf = Pdf::loadView('reports.pdf', compact(
            'chama', 'members', 'contributions', 'loans',
            'totalContributions', 'totalLoans', 'totalRepayments', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('smartchama-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Helper: build member breakdown ────────────────────────────
    private function buildMemberBreakdown(int $chamaId, $from = null, $to = null)
    {
        return User::where('chama_id', $chamaId)
            ->where('status', 'active')
            ->get()
            ->map(function ($member) use ($from, $to) {
                $query = Contribution::where('user_id', $member->id)->where('status', 'completed');
                if ($from && $to) $query->whereBetween('created_at', [$from, $to]);

                $member->total         = $query->sum('amount');
                $member->payment_count = $query->count();
                $member->last_payment  = $query->latest()->value('created_at')?->format('d M Y');
                return $member;
            });
    }
}