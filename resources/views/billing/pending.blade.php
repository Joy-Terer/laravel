{{-- ============================================================
     FILE: resources/views/billing/pending.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Payment Pending')
@section('page-title', 'Payment Pending')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-5">
    <div class="card text-center" style="padding:48px 32px">
        <div style="width:72px;height:72px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px">
            ⏳
        </div>
        <h2 style="font-size:20px;font-weight:700;margin-bottom:8px">Waiting for Payment</h2>
        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:24px;line-height:1.6">
            An M-Pesa payment prompt has been sent to your phone.
            Enter your PIN to confirm. This page will update automatically.
        </p>
        <div class="alert-custom alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Do not close this page. Payment confirmation may take up to 2 minutes.</span>
        </div>
        <div style="margin-top:24px;display:flex;gap:10px;justify-content:center">
            <a href="{{ route('billing.plans') }}" class="btn-outline-custom">Back to Plans</a>
            <a href="{{ route('dashboard') }}" class="btn-primary-custom">Go to Dashboard</a>
        </div>
    </div>
</div>
</div>
@endsection