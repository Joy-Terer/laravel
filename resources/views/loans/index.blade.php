@extends('layouts.app')
@section('title', 'Loans')
@section('page-title', 'Loan Management')
@section('page-subtitle', 'Your loans and repayments')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-bank text-warning-custom"></i>
            </div>
            <div class="stat-label">Total Borrowed</div>
            <div class="stat-value">KES {{ number_format($loans->sum('amount'), 0) }}</div>
            <div class="stat-change text-secondary-custom">All time</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light">
                <i class="bi bi-hourglass-split text-danger-custom"></i>
            </div>
            <div class="stat-label">Outstanding Balance</div>
            <div class="stat-value">
                KES {{ number_format($loans->whereIn('status', ['approved', 'active'])->sum('balance'), 0) }}
            </div>
            <div class="stat-change text-danger-custom">Remaining</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="bi bi-check-circle text-success-custom"></i>
            </div>
            <div class="stat-label">Fully Repaid</div>
            <div class="stat-value">{{ $loans->where('status', 'repaid')->count() }}</div>
            <div class="stat-change text-success-custom">Completed</div>
        </div>
    </div>
</div>

<!-- LOANS LIST -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">My Loans</span>
        <a href="{{ route('loans.apply') }}" class="btn-primary-custom btn-sm">
            <i class="bi bi-plus-lg"></i> Apply for Loan
        </a>
    </div>

    <div class="card-body-custom">
        @forelse($loans as $loan)
            <div class="loan-card">

                <!-- LOAN HEADER -->
                <div class="loan-card-header">
                    <div>
                        <div class="loan-card-amount">KES {{ number_format($loan->amount, 0) }}</div>
                        <div class="loan-card-meta">
                            {{ $loan->purpose }}
                            &nbsp;·&nbsp;
                            {{ $loan->repayment_period }} month(s)
                            &nbsp;·&nbsp;
                            Applied {{ $loan->created_at->format('d M Y') }}
                        </div>
                        @if($loan->decline_reason)
                            <div class="loan-decline-reason">
                                <i class="bi bi-info-circle"></i> {{ $loan->decline_reason }}
                            </div>
                        @endif
                    </div>
                    <div class="loan-card-status">
                        @if($loan->status === 'pending')
                            <span class="badge-custom badge-warning">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                        @elseif($loan->status === 'approved')
                            <span class="badge-custom badge-primary">
                                <i class="bi bi-check-circle"></i> Approved
                            </span>
                        @elseif($loan->status === 'repaid')
                            <span class="badge-custom badge-success">
                                <i class="bi bi-check-circle-fill"></i> Repaid
                            </span>
                        @else
                            <span class="badge-custom badge-danger">
                                <i class="bi bi-x-circle"></i> Declined
                            </span>
                        @endif
                    </div>
                </div>

                <!-- PROGRESS BAR (approved loans only) -->
                @if(in_array($loan->status, ['approved', 'active']))
                    @php $pct = $loan->amount > 0 ? round((($loan->amount - $loan->balance) / $loan->amount) * 100) : 0; @endphp

                    <div class="loan-progress-section">
                        <div class="loan-progress-labels">
                            <span>Balance: <strong class="text-danger-custom">KES {{ number_format($loan->balance, 0) }}</strong></span>
                            <span>{{ 100 - $pct }}% remaining</span>
                        </div>
                        <div class="progress-custom">
                            <div class="progress-bar-custom bg-success-bar" style="width:{{ $pct }}%"></div>
                        </div>
                        @if($loan->due_date)
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                Due: {{ $loan->due_date->format('d M Y') }}
                                @if($loan->isOverdue())
                                    <span class="text-danger-custom font-semibold"> — OVERDUE</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- REPAY BUTTON -->
                    @if($loan->user_id === auth()->id())
                        <button class="btn-primary-custom btn-sm"
                                onclick="document.getElementById('repay-{{ $loan->id }}').style.display='block';this.style.display='none'">
                            <i class="bi bi-cash"></i> Make Repayment
                        </button>

                        <!-- INLINE REPAYMENT FORM -->
                        <div id="repay-{{ $loan->id }}" class="repay-panel">
                            <div class="repay-panel-title">Make a Repayment</div>
                            <form method="POST" action="{{ route('loans.repay', $loan->id) }}" class="repay-form">
                                @csrf
                                <div>
                                    <label class="form-label-custom">Amount (KES)</label>
                                    <input type="number" name="amount"
                                           class="form-control-custom"
                                           placeholder="e.g. 1000"
                                           min="1"
                                           max="{{ $loan->balance }}"
                                           required>
                                </div>
                                <div>
                                    <label class="form-label-custom">Payment Method</label>
                                    <select name="payment_method" class="form-control-custom">
                                        <option value="mpesa">M-Pesa</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="wave">Wave</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn-primary-custom btn-sm">
                                        <i class="bi bi-check-lg"></i> Confirm
                                    </button>
                                    <button type="button" class="btn-outline-custom btn-sm"
                                            onclick="document.getElementById('repay-{{ $loan->id }}').style.display='none';
                                                     this.closest('.loan-card').querySelector('.btn-primary-custom').style.display='inline-flex'">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- TREASURER/ADMIN APPROVE DECLINE -->
                    @if(in_array(auth()->user()->role, ['treasurer', 'admin']) && $loan->status === 'pending')
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('loans.approve', $loan->id) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-primary-custom btn-sm">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('loans.decline', $loan->id) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-danger-custom btn-sm">
                                    <i class="bi bi-x-lg"></i> Decline
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-bank"></i>
                <h3>No loans yet</h3>
                <p>Apply for a loan from your chama group</p>
                <a href="{{ route('loans.apply') }}" class="btn-primary-custom" style="margin-top:14px">
                    <i class="bi bi-plus-lg"></i> Apply for a Loan
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection