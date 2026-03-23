@extends('layouts.app')
@section('title', 'Treasurer Dashboard')
@section('page-title', 'Treasurer Dashboard')
@section('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="bi bi-cash-stack text-success-custom"></i>
            </div>
            <div class="stat-label">Group Balance</div>
            <div class="stat-value">KES {{ number_format($chama->balance ?? 0, 0) }}</div>
            <div class="stat-change text-success-custom">
                <i class="bi bi-arrow-up-short"></i> Total funds
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="bi bi-people text-primary-custom"></i>
            </div>
            <div class="stat-label">Active Members</div>
            <div class="stat-value">{{ $totalMembers }}</div>
            <div class="stat-change text-secondary-custom">
                <i class="bi bi-person-check"></i> Verified
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-calendar-check text-warning-custom"></i>
            </div>
            <div class="stat-label">This Month</div>
            <div class="stat-value">KES {{ number_format($monthlyContributions, 0) }}</div>
            <div class="stat-change text-secondary-custom">
                <i class="bi bi-calendar3"></i> {{ now()->format('F Y') }}
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light">
                <i class="bi bi-hourglass-split text-danger-custom"></i>
            </div>
            <div class="stat-label">Pending Loans</div>
            <div class="stat-value">{{ $pendingLoans }}</div>
            <div class="stat-change {{ $pendingLoans > 0 ? 'text-danger-custom' : 'text-success-custom' }}">
                {{ $pendingLoans > 0 ? 'Awaiting review' : 'None pending' }}
            </div>
        </div>
    </div>
</div>

<!-- MAIN ROW -->
<div class="row g-3">

    <!-- RECENT CONTRIBUTIONS TABLE -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Recent Contributions</span>
                <a href="{{ route('contributions.index') }}" class="btn-outline-custom btn-sm">View all</a>
            </div>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentContributions as $c)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="member-avatar avatar-sm avatar-blue">
                                    {{ strtoupper(substr($c->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold" style="font-size:13px">{{ $c->user->name }}</div>
                                    <div class="text-muted-custom" style="font-size:11px">{{ $c->user->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-bold">KES {{ number_format($c->amount, 0) }}</td>
                        <td>
                            <span class="badge-custom badge-primary">{{ ucfirst($c->payment_method) }}</span>
                        </td>
                        <td class="text-muted-custom" style="font-size:12px">{{ $c->created_at->format('d M Y') }}</td>
                        <td>
                            @if($c->status === 'completed')
                                <span class="badge-custom badge-success">Confirmed</span>
                            @elseif($c->status === 'pending')
                                <span class="badge-custom badge-warning">Pending</span>
                            @else
                                <span class="badge-custom badge-danger">Failed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted-custom" style="padding:30px">No contributions yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PENDING LOAN REQUESTS -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Pending Loan Requests</span>
                <a href="{{ route('loans.index') }}" class="btn-outline-custom btn-sm">All loans</a>
            </div>
            <div class="card-body-custom">
                @forelse($activeLoans->where('status','pending') as $loan)
                    <div class="loan-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="font-semibold" style="font-size:13px">{{ $loan->user->name }}</div>
                            <span class="badge-custom badge-warning">Pending</span>
                        </div>
                        <div class="loan-amount">KES {{ number_format($loan->amount, 0) }}</div>
                        <div class="loan-meta">{{ $loan->purpose }} · {{ $loan->repayment_period }} month(s)</div>
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('loans.approve', $loan->id) }}" class="flex-grow-1">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-primary-custom btn-sm w-100 justify-content-center">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('loans.decline', $loan->id) }}" class="flex-grow-1">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-danger-custom btn-sm w-100 justify-content-center">
                                    <i class="bi bi-x-lg"></i> Decline
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:30px 0">
                        <i class="bi bi-inbox"></i>
                        <h3>No pending requests</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection