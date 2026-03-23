<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Report — {{ $chama->name }}</title>
    <style>
        /* DomPDF uses inline styles — no external CSS */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: white;
            padding: 0;
        }

        /* Header */
        .report-header {
            background: #0f172a;
            color: white;
            padding: 28px 32px;
            margin-bottom: 24px;
        }
        .report-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .report-brand { font-size: 20px; font-weight: 700; color: white; }
        .report-brand-sub { font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .report-title { font-size: 16px; font-weight: 700; color: white; text-align: right; }
        .report-period { font-size: 10px; color: #93c5fd; text-align: right; margin-top: 3px; }

        .report-meta {
            display: flex;
            gap: 24px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .meta-item { font-size: 10px; color: #94a3b8; }
        .meta-item strong { display: block; font-size: 12px; color: white; margin-bottom: 2px; }

        /* Content */
        .report-body { padding: 0 32px 32px; }

        /* Summary cards */
        .summary-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .summary-card {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
        }
        .summary-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .summary-value { font-size: 18px; font-weight: 700; color: #0f172a; }
        .summary-sub   { font-size: 9px; color: #64748b; margin-top: 3px; }

        /* Section heading */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 6px;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 16px; }
        thead th {
            background: #1d4ed8;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #1a1a2e;
        }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
        }
        .badge-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

        /* Footer */
        .report-footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        .text-right { text-align: right; }
        .text-bold  { font-weight: 700; }
        .text-green { color: #16a34a; }
        .text-red   { color: #dc2626; }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="report-header">
    <div class="report-header-top">
        <div>
            <div class="report-brand">SmartChama</div>
            <div class="report-brand-sub">Savings Platform</div>
        </div>
        <div>
            <div class="report-title">Financial Report</div>
            <div class="report-period">{{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</div>
        </div>
    </div>
    <div class="report-meta">
        <div class="meta-item">
            <strong>{{ $chama->name }}</strong>
            Chama Group
        </div>
        <div class="meta-item">
            <strong>{{ $members->count() }}</strong>
            Active Members
        </div>
        <div class="meta-item">
            <strong>KES {{ number_format($chama->balance, 0) }}</strong>
            Current Balance
        </div>
        <div class="meta-item">
            <strong>{{ now()->format('d M Y, h:i A') }}</strong>
            Generated
        </div>
        <div class="meta-item">
            <strong>{{ auth()->user()->name }}</strong>
            Generated By
        </div>
    </div>
</div>

<div class="report-body">

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Contributions</div>
            <div class="summary-value text-green">KES {{ number_format($totalContributions, 0) }}</div>
            <div class="summary-sub">For selected period</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Loans Disbursed</div>
            <div class="summary-value text-red">KES {{ number_format($totalLoans, 0) }}</div>
            <div class="summary-sub">For selected period</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Repayments Received</div>
            <div class="summary-value text-green">KES {{ number_format($totalRepayments, 0) }}</div>
            <div class="summary-sub">For selected period</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Group Balance</div>
            <div class="summary-value">KES {{ number_format($chama->balance, 0) }}</div>
            <div class="summary-sub">Current balance</div>
        </div>
    </div>

    <!-- CONTRIBUTIONS TABLE -->
    <div class="section-title">Member Contributions</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contributions as $c)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-bold">{{ $c->user->name }}</td>
                <td class="text-bold">KES {{ number_format($c->amount, 0) }}</td>
                <td>{{ ucfirst($c->payment_method) }}</td>
                <td>{{ $c->transaction_code ?? '—' }}</td>
                <td>{{ $c->created_at->format('d M Y') }}</td>
                <td>
                    @if($c->status === 'completed')
                        <span class="badge badge-green">Confirmed</span>
                    @elseif($c->status === 'pending')
                        <span class="badge badge-yellow">Pending</span>
                    @else
                        <span class="badge badge-red">Failed</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:16px">No contributions in this period</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="text-align:right;font-size:11px;font-weight:700;margin-bottom:20px">
        Period Total: KES {{ number_format($totalContributions, 0) }}
    </div>

    <!-- LOANS TABLE -->
    <div class="section-title">Loans Summary</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Amount</th>
                <th>Purpose</th>
                <th>Period</th>
                <th>Due Date</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-bold">{{ $loan->user->name }}</td>
                <td>KES {{ number_format($loan->amount, 0) }}</td>
                <td>{{ $loan->purpose }}</td>
                <td>{{ $loan->repayment_period }} month(s)</td>
                <td>{{ $loan->due_date ? $loan->due_date->format('d M Y') : '—' }}</td>
                <td class="{{ $loan->balance > 0 ? 'text-red' : 'text-green' }}">
                    KES {{ number_format($loan->balance, 0) }}
                </td>
                <td>
                    @if($loan->status === 'approved')   <span class="badge badge-blue">Approved</span>
                    @elseif($loan->status === 'repaid')  <span class="badge badge-green">Repaid</span>
                    @elseif($loan->status === 'pending') <span class="badge badge-yellow">Pending</span>
                    @else <span class="badge badge-red">Declined</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:16px">No loans in this period</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- MEMBER COMPLIANCE -->
    <div class="section-title">Member Contribution Compliance</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Role</th>
                <th>Total Contributed</th>
                <th>Payments Made</th>
                <th>Compliance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            @php
                $memberTotal = $contributions->where('user_id', $member->id)->where('status','completed')->sum('amount');
                $paymentCount = $contributions->where('user_id', $member->id)->count();
                $compliant = $memberTotal >= ($chama->contribution_amount ?? 2000);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-bold">{{ $member->name }}</td>
                <td>{{ ucfirst($member->role) }}</td>
                <td class="{{ $memberTotal > 0 ? 'text-green text-bold' : '' }}">KES {{ number_format($memberTotal, 0) }}</td>
                <td>{{ $paymentCount }}</td>
                <td>
                    @if($compliant)
                        <span class="badge badge-green">Compliant</span>
                    @else
                        <span class="badge badge-yellow">Partial</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- FOOTER -->
<div class="report-footer" style="padding:0 32px 32px">
    <div>
        <div>Smart Chama Funding and Contribution System</div>
        <div>This is a computer-generated report and does not require a signature.</div>
    </div>
    <div class="text-right">
        <div>Generated: {{ now()->format('d M Y, h:i A') }}</div>
        <div>By: {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</div>
    </div>
</div>

</body>
</html>