@extends('layouts.app')
@section('title', 'Billing History')
@section('page-title', 'Billing History')
@section('page-subtitle', 'Your subscription invoices')

@section('content')

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">Invoices</span>
        <a href="{{ route('billing.plans') }}" class="btn-outline-custom btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
    </div>

    @if($invoices->count() > 0)
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td class="font-mono" style="font-size:12px">{{ $invoice->invoice_number }}</td>
                    <td class="font-semibold">{{ $invoice->subscription->plan->name ?? '—' }}</td>
                    <td>
                        <div class="font-bold">KES {{ number_format($invoice->amount_kes, 0) }}</div>
                        <div class="text-muted-custom" style="font-size:11px">${{ number_format($invoice->amount_usd, 2) }}</div>
                    </td>
                    <td>
                        <span class="badge-custom badge-primary">{{ ucfirst($invoice->payment_method) }}</span>
                    </td>
                    <td class="text-muted-custom" style="font-size:12px">
                        {{ $invoice->created_at->format('d M Y') }}
                    </td>
                    <td>
                        @if($invoice->status === 'paid')
                            <span class="badge-custom badge-success"><i class="bi bi-check-circle"></i> Paid</span>
                        @elseif($invoice->status === 'pending')
                            <span class="badge-custom badge-warning"><i class="bi bi-clock"></i> Pending</span>
                        @else
                            <span class="badge-custom badge-danger"><i class="bi bi-x-circle"></i> Failed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-pagination">{{ $invoices->links() }}</div>
    @else
        <div class="empty-state">
            <i class="bi bi-receipt"></i>
            <h3>No invoices yet</h3>
            <p>Your billing history will appear here once you upgrade.</p>
        </div>
    @endif
</div>

@endsection