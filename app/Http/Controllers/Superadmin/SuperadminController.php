<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Chama;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperadminController extends Controller
{
    // ── Login ──────────────────────────────────────────────────────
    public function showLogin()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('superadmin')->attempt($request->only('email', 'password'))) {
            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid superadmin credentials.']);
    }

    public function logout()
    {
        Auth::guard('superadmin')->logout();
        return redirect()->route('superadmin.login');
    }

    // ── Dashboard ──────────────────────────────────────────────────
    public function dashboard()
    {
        $totalChamas        = Chama::count();
        $totalMembers       = User::count();
        $totalRevenue       = SubscriptionInvoice::where('status', 'paid')->sum('amount_kes');
        $activeSubscriptions= Subscription::where('status', 'active')->count();

        $recentChamas = Chama::with('plan')->latest()->take(10)->get();

        $planStats = Plan::withCount(['subscriptions' => fn($q) =>
            $q->where('status', 'active')
        ])->get();

        $monthlyRevenue = collect(range(5, 0))->map(fn($i) => [
            'month'   => now()->subMonths($i)->format('M Y'),
            'revenue' => SubscriptionInvoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->subMonths($i)->month)
                ->whereYear('paid_at',  now()->subMonths($i)->year)
                ->sum('amount_kes'),
        ]);

        return view('superadmin.dashboard', compact(
            'totalChamas', 'totalMembers', 'totalRevenue',
            'activeSubscriptions', 'recentChamas', 'planStats', 'monthlyRevenue'
        ));
    }

    // ── All chamas ─────────────────────────────────────────────────
    public function chamas(Request $request)
    {
        $query = Chama::with(['plan', 'members']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        if ($request->filled('plan')) {
            $query->whereHas('plan', fn($q) => $q->where('slug', $request->plan));
        }

        $chamas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $plans  = Plan::all();

        return view('superadmin.chamas', compact('chamas', 'plans'));
    }

    // ── All subscriptions ──────────────────────────────────────────
    public function subscriptions()
    {
        $subscriptions = Subscription::with(['chama', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('superadmin.subscriptions', compact('subscriptions'));
    }

    // ── Revenue / invoices ─────────────────────────────────────────
    public function revenue()
    {
        $invoices = SubscriptionInvoice::with(['chama', 'subscription.plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalRevenue   = SubscriptionInvoice::where('status', 'paid')->sum('amount_kes');
        $monthRevenue   = SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)->sum('amount_kes');
        $pendingRevenue = SubscriptionInvoice::where('status', 'pending')->sum('amount_kes');

        return view('superadmin.revenue', compact(
            'invoices', 'totalRevenue', 'monthRevenue', 'pendingRevenue'
        ));
    }

    // ── Manage plans ───────────────────────────────────────────────
    public function plans()
    {
        $plans = Plan::withCount('subscriptions')->orderBy('sort_order')->get();
        return view('superadmin.plans', compact('plans'));
    }

    // ── Toggle chama active/inactive ───────────────────────────────
    public function toggleChama(Chama $chama)
    {
        $chama->update(['is_active' => !$chama->is_active]);
        $status = $chama->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "{$chama->name} has been {$status}.");
    }

    // ── Force assign plan to chama ─────────────────────────────────
    public function assignPlan(Request $request, Chama $chama)
    {
        $request->validate(['plan_id' => ['required', 'exists:plans,id']]);
        $chama->update(['plan_id' => $request->plan_id]);
        return back()->with('success', "Plan updated for {$chama->name}.");
    }
}