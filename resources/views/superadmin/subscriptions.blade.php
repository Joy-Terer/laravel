@extends('superadmin.layout')
@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('content')

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Subscriptions ({{ $subscriptions->total() }})</span>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Chama</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Billing</th>
                <th>Started</th>
                <th>Expires</th>
                <th>Days Left</th>
                <th>Auto Renew</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $sub)
            <tr>
                <td>
                    <div class="font-semibold" style="font-size:13px">{{ $sub->chama->name }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $sub->chama->code }}</div>
                </td>
                <td>
                    <span class="badge-custom {{ $sub->plan->slug === 'free' ? 'badge-grey' : 'badge-primary' }}">
                        {{ $sub->plan->name }}
                    </span>
                </td>
                <td>
                    @if($sub->status === 'active')
                        <span class="badge-custom badge-success">
                            <i class="bi bi-check-circle"></i> Active
                        </span>
                    @elseif($sub->status === 'cancelled')
                        <span class="badge-custom badge-danger">
                            <i class="bi bi-x-circle"></i> Cancelled
                        </span>
                    @elseif($sub->status === 'expired')
                        <span class="badge-custom badge-grey">
                            <i class="bi bi-clock-history"></i> Expired
                        </span>
                    @elseif($sub->status === 'trial')
                        <span class="badge-custom badge-info">
                            <i class="bi bi-stars"></i> Trial
                        </span>
                    @else
                        <span class="badge-custom badge-warning">{{ ucfirst($sub->status) }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge-custom badge-grey">{{ ucfirst($sub->billing_cycle) }}</span>
                </td>
                <td class="text-muted-custom" style="font-size:12px">
                    {{ $sub->starts_at->format('d M Y') }}
                </td>
                <td class="text-muted-custom" style="font-size:12px">
                    {{ $sub->ends_at?->format('d M Y') ?? 'No expiry' }}
                </td>
                <td>
                    @if($sub->ends_at)
                        @php $days = $sub->daysRemaining(); @endphp
                        <span class="{{ $days <= 5 ? 'text-danger-custom' : ($days <= 10 ? 'text-warning-custom' : 'text-success-custom') }} font-semibold" style="font-size:12px">
                            {{ $days }} days
                        </span>
                    @else
                        <span class="text-muted-custom" style="font-size:12px">—</span>
                    @endif
                </td>
                <td>
                    @if($sub->auto_renew)
                        <span class="badge-custom badge-success">
                            <i class="bi bi-arrow-repeat"></i> Yes
                        </span>
                    @else
                        <span class="badge-custom badge-grey">No</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="bi bi-credit-card"></i>
                        <h3>No subscriptions yet</h3>
                        <p>Subscriptions will appear here when chamas upgrade</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-pagination">{{ $subscriptions->links() }}</div>
</div>

@endsection