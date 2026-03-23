{{-- ============================================================
     FILE: resources/views/reports/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title','Reports')
@section('page-title','Financial Reports')
@section('page-subtitle','Generate and export group reports')

@section('content')

<div class="row g-3 mb-4">

    <!-- REPORT GENERATOR FORM -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Generate Report</span>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('reports.generate') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label-custom">Report Type</label>
                        <select name="type" class="form-control-custom" required>
                            <option value="monthly">Monthly Report</option>
                            <option value="annual">Annual Report</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label-custom">From Date</label>
                        <input type="date" name="from" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label-custom">To Date</label>
                        <input type="date" name="to" value="{{ now()->format('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <button type="submit" class="btn-primary-custom w-100 justify-content-center mb-2">
                        <i class="bi bi-bar-chart-line"></i> Generate Report
                    </button>
                    <a href="{{ route('reports.pdf') }}" target="_blank" class="btn-outline-custom w-100 justify-content-center">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- SUMMARY STATS -->
    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-light"><i class="bi bi-cash-stack text-primary-custom"></i></div>
                    <div class="stat-label">Total Contributions</div>
                    <div class="stat-value">KES {{ number_format($totalContributions ?? 0, 0) }}</div>
                    <div class="stat-change text-secondary-custom">Period total</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-success-light"><i class="bi bi-bank text-success-custom"></i></div>
                    <div class="stat-label">Group Balance</div>
                    <div class="stat-value">KES {{ number_format(auth()->user()->chama->balance ?? 0, 0) }}</div>
                    <div class="stat-change text-success-custom"><i class="bi bi-arrow-up-short"></i> Current</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-danger-light"><i class="bi bi-bank2 text-danger-custom"></i></div>
                    <div class="stat-label">Loans Disbursed</div>
                    <div class="stat-value">KES {{ number_format($totalLoans ?? 0, 0) }}</div>
                    <div class="stat-change text-secondary-custom">Period total</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-info-light"><i class="bi bi-arrow-down-circle text-info-custom"></i></div>
                    <div class="stat-label">Repayments</div>
                    <div class="stat-value">KES {{ number_format($totalRepayments ?? 0, 0) }}</div>
                    <div class="stat-change text-success-custom">Period total</div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MEMBER BREAKDOWN TABLE -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">Member Contributions Breakdown</span>
        <span class="text-muted-custom" style="font-size:12px">
            {{ isset($from) ? $from->format('d M Y').' — '.$to->format('d M Y') : 'All time' }}
        </span>
    </div>
    <table class="table-custom">
        <thead>
            <tr>
                <th>Member</th>
                <th>Total Contributed</th>
                <th>Last Payment</th>
                <th>Payments</th>
                <th>Compliance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($memberBreakdown ?? [] as $row)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="member-avatar avatar-sm avatar-blue">{{ strtoupper(substr($row->name,0,2)) }}</div>
                        <div>
                            <div class="font-semibold" style="font-size:13px">{{ $row->name }}</div>
                            <div class="text-muted-custom" style="font-size:11px">{{ ucfirst($row->role) }}</div>
                        </div>
                    </div>
                </td>
                <td class="font-bold">KES {{ number_format($row->total ?? 0, 0) }}</td>
                <td class="text-muted-custom" style="font-size:12px">{{ $row->last_payment ?? '—' }}</td>
                <td>{{ $row->payment_count ?? 0 }}</td>
                <td>
                    @if(($row->total ?? 0) >= (auth()->user()->chama->contribution_amount ?? 2000))
                        <span class="badge-custom badge-success">Compliant</span>
                    @else
                        <span class="badge-custom badge-warning">Partial</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted-custom" style="padding:30px">No data for this period</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection