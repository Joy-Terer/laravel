@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')
@section('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="bi bi-wallet2 text-primary-custom"></i>
            </div>
            <div class="stat-label">Total Contributed</div>
            <div class="stat-value">KES {{ number_format($totalContributions, 0) }}</div>
            <div class="stat-change text-success-custom">
                <i class="bi bi-arrow-up-short"></i> All time
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="bi bi-bank text-success-custom"></i>
            </div>
            <div class="stat-label">Group Balance</div>
            <div class="stat-value">KES {{ number_format($user->chama->balance ?? 0, 0) }}</div>
            <div class="stat-change text-secondary-custom">
                <i class="bi bi-people"></i>
                {{ $user->chama->members()->where('status','active')->count() }} active members
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-cash-coin text-warning-custom"></i>
            </div>
            <div class="stat-label">Active Loan</div>
            <div class="stat-value">KES {{ number_format($loanBalance, 0) }}</div>
            <div class="stat-change {{ $loanBalance > 0 ? 'text-warning-custom' : 'text-success-custom' }}">
                @if($loanBalance > 0)
                    <i class="bi bi-exclamation-circle"></i> Balance remaining
                @else
                    <i class="bi bi-check-circle"></i> No active loan
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-light">
                <i class="bi bi-calendar-check text-info-custom"></i>
            </div>
            <div class="stat-label">This Month</div>
            <div class="stat-value">
                KES {{ number_format(
                    \App\Models\Contribution::where('user_id', auth()->id())
                        ->whereMonth('created_at', now()->month)
                        ->where('status','completed')
                        ->sum('amount'), 0
                ) }}
            </div>
            <div class="stat-change text-secondary-custom">
                <i class="bi bi-calendar3"></i> {{ now()->format('F Y') }}
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT ROW -->
<div class="row g-3 mb-4">

    <!-- RECENT CONTRIBUTIONS -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Recent Contributions</span>
                <a href="{{ route('contributions.index') }}" class="btn-outline-custom btn-sm">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($contributions->count() > 0)
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contributions as $c)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $c->created_at->format('d M Y') }}</div>
                                <div class="text-muted-custom" style="font-size:11px">{{ $c->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="font-bold">KES {{ number_format($c->amount, 0) }}</td>
                            <td>
                                <span class="badge-custom badge-primary">
                                    <i class="bi bi-{{ $c->payment_method === 'mpesa' ? 'phone' : ($c->payment_method === 'paypal' ? 'globe' : 'cash-stack') }}"></i>
                                    {{ ucfirst($c->payment_method) }}
                                </span>
                            </td>
                            <td class="font-mono text-muted-custom" style="font-size:12px">
                                {{ $c->transaction_code ?? '—' }}
                            </td>
                            <td>
                                @if($c->status === 'completed')
                                    <span class="badge-custom badge-success"><i class="bi bi-check-circle"></i> Confirmed</span>
                                @elseif($c->status === 'pending')
                                    <span class="badge-custom badge-warning"><i class="bi bi-clock"></i> Pending</span>
                                @else
                                    <span class="badge-custom badge-danger"><i class="bi bi-x-circle"></i> Failed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>No contributions yet</h3>
                    <p>Make your first contribution to get started</p>
                    <a href="{{ route('contributions.create') }}" class="btn-primary-custom" style="margin-top:14px">
                        <i class="bi bi-plus-lg"></i> Contribute Now
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- SIDEBAR CARDS -->
    <div class="col-lg-4">

        <!-- LOAN SUMMARY -->
        <div class="card mb-3">
            <div class="card-header-custom">
                <span class="card-title-custom">My Loan</span>
                <a href="{{ route('loans.index') }}" class="btn-outline-custom btn-sm">Details</a>
            </div>
            <div class="card-body-custom">
                @if($activeLoans->count() > 0)
                    @php $loan = $activeLoans->first(); @endphp
                    @php $pct = $loan->amount > 0 ? round((($loan->amount - $loan->balance) / $loan->amount) * 100) : 0; @endphp

                    <div class="loan-summary-row">
                        <span class="text-muted-custom" style="font-size:11px">Loan Amount</span>
                        <span class="loan-amount">KES {{ number_format($loan->amount, 0) }}</span>
                    </div>

                    <div class="progress-custom">
                        <div class="progress-bar-custom bg-success-bar" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="loan-progress-labels">
                        <span>Repaid: KES {{ number_format($loan->amount - $loan->balance, 0) }}</span>
                        <span>{{ $pct }}%</span>
                    </div>

                    <div class="loan-meta">
                        Balance: <strong class="text-danger-custom">KES {{ number_format($loan->balance, 0) }}</strong>
                        &nbsp;·&nbsp;
                        Due: <strong>{{ $loan->due_date ? $loan->due_date->format('d M Y') : '—' }}</strong>
                    </div>

                    <a href="{{ route('loans.index') }}" class="btn-primary-custom btn-block">
                        <i class="bi bi-cash"></i> Repay Now
                    </a>
                @else
                    <div class="empty-state" style="padding:24px 0">
                        <i class="bi bi-check-circle" style="color:#16a34a;opacity:1"></i>
                        <h3>No active loan</h3>
                        <p>You can borrow up to 2× your contributions</p>
                        <a href="{{ route('loans.apply') }}" class="btn-outline-custom btn-sm" style="margin-top:10px">
                            <i class="bi bi-plus-lg"></i> Apply for Loan
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Quick Actions</span>
            </div>
            <div class="card-body-custom">
                <div class="quick-action-grid">
                    <a href="{{ route('contributions.create') }}" class="quick-action-item quick-action-blue">
                        <i class="bi bi-wallet2"></i>
                        <span>Contribute</span>
                    </a>
                    <a href="{{ route('loans.apply') }}" class="quick-action-item quick-action-orange">
                        <i class="bi bi-bank"></i>
                        <span>Apply Loan</span>
                    </a>
                    <a href="{{ route('contributions.index') }}" class="quick-action-item quick-action-teal">
                        <i class="bi bi-clock-history"></i>
                        <span>History</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="quick-action-item quick-action-green">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- REMINDER BANNER -->
@php
    $nextDue  = now()->endOfMonth();
    $daysLeft = now()->diffInDays($nextDue);
@endphp
@if($daysLeft <= 7)
    <div class="alert-custom alert-warning">
        <i class="bi bi-bell-fill"></i>
        <span>
            Reminder: Your contribution of
            <strong>KES {{ number_format($user->chama->contribution_amount ?? 2000, 0) }}</strong>
            is due on {{ $nextDue->format('d M Y') }} — {{ $daysLeft }} day(s) remaining.
        </span>
        <a href="{{ route('contributions.create') }}" class="btn-warning-custom btn-sm" style="margin-left:auto">
            Pay Now
        </a>
    </div>
@endif

@endsection