{{-- ============================================================
     FILE: resources/views/contributions/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title','Contributions')
@section('page-title','Contributions')
@section('page-subtitle','Your contribution history')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-wallet2 text-primary-custom"></i></div>
            <div class="stat-label">Total Contributed</div>
            <div class="stat-value">KES {{ number_format($contributions->where('status','completed')->sum('amount'), 0) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-calendar-check text-warning-custom"></i></div>
            <div class="stat-label">This Month</div>
            <div class="stat-value">KES {{ number_format($contributions->getCollection()->where('status','completed')->filter(fn($c) => $c->created_at->month === now()->month)->sum('amount'), 0) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-receipt text-success-custom"></i></div>
            <div class="stat-label">Total Payments</div>
            <div class="stat-value">{{ $contributions->count() }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Contributions</span>
        <a href="{{ route('contributions.create') }}" class="btn-primary-custom btn-sm">
            <i class="bi bi-plus-lg"></i> New Contribution
        </a>
    </div>

    <div class="table-filters">
        <form method="GET" class="filters-form">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control-custom filter-input" placeholder="From">
            <input type="date" name="to"   value="{{ request('to') }}"   class="form-control-custom filter-input" placeholder="To">
            <select name="method" class="form-control-custom filter-input">
                <option value="">All Methods</option>
                <option value="mpesa"  {{ request('method')==='mpesa'  ? 'selected':'' }}>M-Pesa</option>
                <option value="paypal" {{ request('method')==='paypal' ? 'selected':'' }}>PayPal</option>
                <option value="wave"   {{ request('method')==='wave'   ? 'selected':'' }}>Wave</option>
                <option value="cash"   {{ request('method')==='cash'   ? 'selected':'' }}>Cash</option>
            </select>
            <button type="submit" class="btn-primary-custom btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->anyFilled(['from','to','method']))
                <a href="{{ route('contributions.index') }}" class="btn-outline-custom btn-sm">Clear</a>
            @endif
        </form>
    </div>

    @if($contributions->count() > 0)
        <table class="table-custom">
            <thead>
                <tr>
                    <th>#</th><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contributions as $c)
                <tr>
                    <td class="font-mono text-muted-custom" style="font-size:11px">#{{ $c->id }}</td>
                    <td>
                        <div class="font-semibold">{{ $c->created_at->format('d M Y') }}</div>
                        <div class="text-muted-custom" style="font-size:11px">{{ $c->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="font-bold">KES {{ number_format($c->amount, 2) }}</td>
                    <td>
                        <span class="badge-custom badge-primary">
                            <i class="bi bi-{{ $c->payment_method==='mpesa' ? 'phone' : ($c->payment_method==='paypal' ? 'globe' : ($c->payment_method==='wave' ? 'send' : 'cash')) }}"></i>
                            {{ ucfirst($c->payment_method) }}
                        </span>
                    </td>
                    <td class="font-mono text-muted-custom" style="font-size:12px">{{ $c->transaction_code ?? '—' }}</td>
                    <td>
                        @if($c->status==='completed') <span class="badge-custom badge-success"><i class="bi bi-check-circle"></i> Confirmed</span>
                        @elseif($c->status==='pending') <span class="badge-custom badge-warning"><i class="bi bi-clock"></i> Pending</span>
                        @else <span class="badge-custom badge-danger"><i class="bi bi-x-circle"></i> Failed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-pagination" style="font-size:13px">
    {{ $contributions->links() }}
</div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No contributions found</h3>
            <p>Try adjusting your filters or make a new contribution</p>
        </div>
    @endif
</div>
@endsection