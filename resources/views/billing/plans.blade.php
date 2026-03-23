@extends('layouts.app')
@section('title', 'Plans & Billing')
@section('page-title', 'Plans & Billing')
@section('page-subtitle', 'Choose the right plan for your chama')

@section('content')

@if(session('upgrade'))
    <div class="alert-custom alert-warning">
        <i class="bi bi-arrow-up-circle-fill"></i>
        <span>{{ session('upgrade') }}</span>
    </div>
@endif

@if(isset($subscription) && $subscription)
    <div class="alert-custom alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <span>
            You are on the <strong>{{ $currentPlan->name }}</strong> plan.
            @if($subscription->ends_at)
                Renews on {{ $subscription->ends_at->format('d M Y') }}
                ({{ $subscription->daysRemaining() }} days remaining).
            @endif
        </span>
        <form method="POST" action="{{ route('billing.cancel') }}" style="margin-left:auto">
            @csrf
            <button type="submit" class="btn-outline-custom btn-sm text-danger-custom"
                onclick="return confirm('Are you sure you want to cancel?')">
                Cancel Subscription
            </button>
        </form>
    </div>
@endif

<!-- PLANS GRID -->
<div class="plans-grid">
    @foreach($plans as $plan)
    <div class="plan-card {{ $plan->slug === 'premium' ? 'plan-card-featured' : '' }} {{ $currentPlan?->id === $plan->id ? 'plan-card-current' : '' }}">

        @if($plan->slug === 'premium')
            <div class="plan-popular-badge">Most Popular</div>
        @endif

        @if($currentPlan?->id === $plan->id)
            <div class="plan-current-badge">Current Plan</div>
        @endif

        <div class="plan-header">
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-price">
                @if($plan->isFree())
                    <span class="plan-price-amount">Free</span>
                    <span class="plan-price-period">forever</span>
                @else
                    <span class="plan-price-amount">KES {{ number_format($plan->price_kes, 0) }}</span>
                    <span class="plan-price-period">/month</span>
                    <div class="plan-price-usd">${{ number_format($plan->price_usd, 2) }} USD</div>
                @endif
            </div>
            <div class="plan-desc">{{ $plan->description }}</div>
        </div>

        <div class="plan-features">
            <div class="plan-feature">
                <i class="bi bi-people feature-icon"></i>
                <span>{{ $plan->membersLabel() }} members</span>
            </div>
            <div class="plan-feature">
                <i class="bi bi-phone feature-icon text-success-custom"></i>
                <span>M-Pesa payments</span>
            </div>
            <div class="plan-feature">
                <i class="bi bi-globe feature-icon text-success-custom"></i>
                <span>PayPal & Wave (international)</span>
            </div>
            <div class="plan-feature {{ !$plan->has_pdf_export ? 'feature-disabled' : '' }}">
                <i class="bi bi-file-pdf feature-icon {{ $plan->has_pdf_export ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>PDF reports export</span>
            </div>
            <div class="plan-feature {{ !$plan->has_email_notifications ? 'feature-disabled' : '' }}">
                <i class="bi bi-envelope feature-icon {{ $plan->has_email_notifications ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>Email notifications</span>
            </div>
            <div class="plan-feature {{ !$plan->has_audit_logs ? 'feature-disabled' : '' }}">
                <i class="bi bi-shield-check feature-icon {{ $plan->has_audit_logs ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>Full audit logs</span>
            </div>
            <div class="plan-feature {{ !$plan->has_priority_support ? 'feature-disabled' : '' }}">
                <i class="bi bi-headset feature-icon {{ $plan->has_priority_support ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>Priority support</span>
            </div>
            <div class="plan-feature {{ !$plan->has_multiple_chamas ? 'feature-disabled' : '' }}">
                <i class="bi bi-diagram-3 feature-icon {{ $plan->has_multiple_chamas ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>Multiple chamas</span>
            </div>
            <div class="plan-feature {{ !$plan->has_custom_branding ? 'feature-disabled' : '' }}">
                <i class="bi bi-palette feature-icon {{ $plan->has_custom_branding ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>Custom branding</span>
            </div>
            <div class="plan-feature {{ !$plan->has_api_access ? 'feature-disabled' : '' }}">
                <i class="bi bi-code-square feature-icon {{ $plan->has_api_access ? 'text-success-custom' : 'text-muted-custom' }}"></i>
                <span>API access</span>
            </div>
        </div>

        <div class="plan-action">
            @if($currentPlan?->id === $plan->id)
                <button class="plan-btn plan-btn-current" disabled>Current Plan</button>
            @elseif($plan->isFree())
                <form method="POST" action="{{ route('billing.select', $plan->slug) }}">
                    @csrf
                    <button type="submit" class="plan-btn plan-btn-outline">Downgrade to Free</button>
                </form>
            @else
                <a href="{{ route('billing.checkout', $plan->slug) }}"
                   class="plan-btn {{ $plan->slug === 'premium' ? 'plan-btn-primary' : 'plan-btn-outline' }}">
                    Upgrade to {{ $plan->name }}
                </a>
            @endif
        </div>

    </div>
    @endforeach
</div>

<!-- BILLING HISTORY LINK -->
<div class="text-center" style="margin-top:24px">
    <a href="{{ route('billing.history') }}" class="btn-outline-custom">
        <i class="bi bi-receipt"></i> View Billing History
    </a>
</div>
@endsection


@push('styles')
<style>
.plans-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
.plan-card { background: #fff; border: 1.5px solid var(--card-border); border-radius: var(--radius-lg); padding: 24px; position: relative; display: flex; flex-direction: column; transition: box-shadow .2s, transform .2s; }
.plan-card:hover { box-shadow: var(--card-shadow-hover); transform: translateY(-3px); }
.plan-card-featured { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
.plan-card-current { border-color: var(--success); }
.plan-popular-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--primary); color: white; font-size: 11px; font-weight: 700; padding: 3px 14px; border-radius: 20px; white-space: nowrap; }
.plan-current-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--success); color: white; font-size: 11px; font-weight: 700; padding: 3px 14px; border-radius: 20px; white-space: nowrap; }
.plan-header { margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--card-border); }
.plan-name { font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; }
.plan-price-amount { font-size: 28px; font-weight: 800; color: var(--text-primary); letter-spacing: -1px; }
.plan-price-period { font-size: 13px; color: var(--text-muted); }
.plan-price-usd { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.plan-desc { font-size: 12.5px; color: var(--text-secondary); margin-top: 8px; line-height: 1.5; }
.plan-features { flex: 1; margin-bottom: 20px; }
.plan-feature { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--text-primary); padding: 5px 0; }
.plan-feature.feature-disabled { opacity: .35; text-decoration: line-through; color: var(--text-muted); }
.feature-icon { font-size: 14px; width: 16px; flex-shrink: 0; }
.plan-action { margin-top: auto; }
.plan-btn { width: 100%; padding: 10px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 700; font-family: var(--font-main); cursor: pointer; text-align: center; text-decoration: none; display: block; border: 2px solid transparent; transition: all .15s; }
.plan-btn-primary { background: var(--primary); color: white; border-color: var(--primary); }
.plan-btn-primary:hover { background: var(--primary-dark); }
.plan-btn-outline { background: white; color: var(--primary); border-color: var(--primary); }
.plan-btn-outline:hover { background: var(--primary-light); }
.plan-btn-current { background: var(--success-light); color: var(--success); border-color: var(--success); cursor: default; }
@media (max-width: 992px) { .plans-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 600px)  { .plans-grid { grid-template-columns: 1fr; } }
</style>
@endpush
