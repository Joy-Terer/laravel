@extends('superadmin.layout')
@section('title', 'Plans')
@section('page-title', 'Plan Management')

@section('content')

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Plans</span>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Plan</th>
                <th>Price KES</th>
                <th>Price USD</th>
                <th>Max Members</th>
                <th>Features</th>
                <th>Active Subs</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td>
                    <div class="font-semibold" style="font-size:14px">{{ $plan->name }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $plan->description }}</div>
                </td>
                <td class="font-bold">
                    {{ $plan->price_kes == 0 ? 'Free' : 'KES ' . number_format($plan->price_kes, 0) . '/mo' }}
                </td>
                <td class="font-bold">
                    {{ $plan->price_usd == 0 ? 'Free' : '$' . number_format($plan->price_usd, 2) . '/mo' }}
                </td>
                <td>
                    <span class="font-semibold">
                        {{ $plan->max_members === -1 ? 'Unlimited' : $plan->max_members }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;flex-wrap:wrap;gap:4px">
                        @if($plan->has_pdf_export)
                            <span class="badge-custom badge-success" style="font-size:9px">PDF</span>
                        @endif
                        @if($plan->has_email_notifications)
                            <span class="badge-custom badge-info" style="font-size:9px">Email</span>
                        @endif
                        @if($plan->has_audit_logs)
                            <span class="badge-custom badge-primary" style="font-size:9px">Audit</span>
                        @endif
                        @if($plan->has_multiple_chamas)
                            <span class="badge-custom badge-warning" style="font-size:9px">Multi-Chama</span>
                        @endif
                        @if($plan->has_custom_branding)
                            <span class="badge-custom badge-grey" style="font-size:9px">Branding</span>
                        @endif
                        @if($plan->has_priority_support)
                            <span class="badge-custom badge-success" style="font-size:9px">Support</span>
                        @endif
                        @if($plan->has_api_access)
                            <span class="badge-custom badge-danger" style="font-size:9px">API</span>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="badge-custom badge-primary">
                        {{ $plan->subscriptions_count }} active
                    </span>
                </td>
                <td>
                    @if($plan->is_active)
                        <span class="badge-custom badge-success">Active</span>
                    @else
                        <span class="badge-custom badge-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection