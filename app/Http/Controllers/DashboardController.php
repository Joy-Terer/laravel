<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Loan;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\ChamaNotification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin'     => $this->adminDashboard(),
            'treasurer' => $this->treasurerDashboard(),
            default     => $this->memberDashboard(),
        };
    }

    // ── Member Dashboard ──────────────────────────────────────────
    private function memberDashboard()
    {
        $user = Auth::user();

        $contributions = Contribution::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $activeLoans = Loan::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'active'])
            ->get();

        $totalContributions = Contribution::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $loanBalance = Loan::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'active'])
            ->sum('balance');

        $unreadNotifications = ChamaNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.member', compact(
            'user', 'contributions', 'activeLoans',
            'totalContributions', 'loanBalance', 'unreadNotifications'
        ));
    }

    // ── Treasurer Dashboard ───────────────────────────────────────
    private function treasurerDashboard()
    {
        $user  = Auth::user();
        $chama = $user->chama;

        $totalMembers = User::where('chama_id', $chama->id)
            ->where('status', 'active')->count();

        $monthlyContributions = Contribution::where('chama_id', $chama->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $pendingLoans = Loan::where('chama_id', $chama->id)
            ->where('status', 'pending')->count();

        $activeLoans = Loan::where('chama_id', $chama->id)
            ->whereIn('status', ['pending', 'approved'])
            ->with('user')->get();

        $recentContributions = Contribution::where('chama_id', $chama->id)
            ->with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboard.treasurer', compact(
            'user', 'chama', 'totalMembers', 'monthlyContributions',
            'pendingLoans', 'activeLoans', 'recentContributions'
        ));
    }

    // ── Admin Dashboard ───────────────────────────────────────────
    private function adminDashboard()
    {
        $user  = Auth::user();
        $chama = $user->chama;

        $pendingMembers = User::where('chama_id', $chama->id)
            ->where('status', 'pending')->get();

        $members = User::where('chama_id', $chama->id)
            ->where('status', 'active')->get();

        $totalContributions = Contribution::where('chama_id', $chama->id)
            ->where('status', 'completed')->sum('amount');

        $totalLoans = Loan::where('chama_id', $chama->id)->sum('amount');

        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')->take(10)->get();

        // ── Chart data ────────────────────────────────────────────
        // Last 6 months labels
        $chartLabels = collect(range(5, 0))->map(fn($i) =>
            now()->subMonths($i)->format('M Y')
        )->toArray();

        // Last 6 months contribution amounts
        $chartAmounts = collect(range(5, 0))->map(fn($i) =>
            (float) Contribution::where('chama_id', $chama->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->whereYear('created_at',  now()->subMonths($i)->year)
                ->sum('amount')
        )->toArray();

        // Payment method counts
        $paymentMethods = [
            'mpesa'  => Contribution::where('chama_id', $chama->id)->where('payment_method', 'mpesa')->count(),
            'paypal' => Contribution::where('chama_id', $chama->id)->where('payment_method', 'paypal')->count(),
            'wave'   => Contribution::where('chama_id', $chama->id)->where('payment_method', 'wave')->count(),
            'cash'   => Contribution::where('chama_id', $chama->id)->where('payment_method', 'cash')->count(),
        ];

        return view('dashboard.admin', compact(
            'user', 'chama', 'pendingMembers', 'members',
            'totalContributions', 'totalLoans', 'recentActivities',
            'chartLabels', 'chartAmounts', 'paymentMethods'
        ));
    }
}