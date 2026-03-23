@extends('superadmin.layout')
@section('title', 'Revenue')
@section('page-title', 'Revenue & Invoices')

@section('content')

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="bi bi-cash-stack text-success-custom"></i>
            </div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">KES {{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-change text-secondary-custom">All time</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="bi bi-calendar-check text-primary-custom"></i>
            </div>
            <div class="stat-label">This Month</div>
            <div class="stat-value">KES {{ number_format($monthRevenue, 0) }}</div>
            <div class="stat-change text-secondary-custom">{{ now()->format('F Y') }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-hourglass-split text-warning-custom"></i>
            </div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">KES {{ number_format($pendingRevenue, 0) }}</div>
            <div class="stat-change text-warning-custom">Awaiting payment</div>
        </div>
    </div>
</div>

<!-- INVOICES TABLE -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Invoices</span>
        <span class="text-muted-custom" style="font-size:12px">{{ $invoices->total() }} total</span>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Chama</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="font-mono" style="font-size:11px">{{ $invoice->invoice_number }}</td>
                <td>
                    <div class="font-semibold" style="font-size:13px">{{ $invoice->chama->name }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $invoice->chama->code }}</div>
                </td>
                <td>
                    <span class="badge-custom badge-primary">
                        {{ $invoice->subscription->plan->name ?? '—' }}
                    </span>
                </td>
                <td>
                    <div class="font-bold">KES {{ number_format($invoice->amount_kes, 0) }}</div>
                    <div class="text-muted-custom" style="font-size:11px">
                        ${{ number_format($invoice->amount_usd, 2) }} USD
                    </div>
                </td>
                <td>
                    <span class="badge-custom {{ $invoice->payment_method === 'mpesa' ? 'badge-success' : 'badge-info' }}">
                        {{ ucfirst($invoice->payment_method ?? '—') }}
                    </span>
                </td>
                <td class="font-mono text-muted-custom" style="font-size:11px">
                    {{ $invoice->transaction_code ?? '—' }}
                </td>
                <td class="text-muted-custom" style="font-size:12px">
                    <div>{{ $invoice->created_at->format('d M Y') }}</div>
                    @if($invoice->paid_at)
                        <div style="font-size:10px;color:#16a34a">Paid {{ $invoice->paid_at->format('d M Y') }}</div>
                    @endif
                </td>
                <td>
                    @if($invoice->status === 'paid')
                        <span class="badge-custom badge-success">
                            <i class="bi bi-check-circle"></i> Paid
                        </span>
                    @elseif($invoice->status === 'pending')
                        <span class="badge-custom badge-warning">
                            <i class="bi bi-clock"></i> Pending
                        </span>
                    @else
                        <span class="badge-custom badge-danger">
                            <i class="bi bi-x-circle"></i> Failed
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="bi bi-receipt"></i>
                        <h3>No invoices yet</h3>
                        <p>Subscription payments will appear here</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-pagination">{{ $invoices->links() }}</div>
</div>

@endsection