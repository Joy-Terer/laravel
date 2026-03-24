@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-people text-primary-custom"></i></div>
            <div class="stat-label">Total Members</div>
            <div class="stat-value">{{ $members->count() }}</div>
            <div class="stat-change {{ $pendingMembers->count() > 0 ? 'text-warning-custom' : 'text-success-custom' }}">
                @if($pendingMembers->count() > 0)
                    <i class="bi bi-clock"></i> {{ $pendingMembers->count() }} pending approval
                @else
                    <i class="bi bi-check-circle"></i> All approved
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-cash-stack text-success-custom"></i></div>
            <div class="stat-label">Group Balance</div>
            <div class="stat-value">KES {{ number_format($chama->balance ?? 0, 0) }}</div>
            <div class="stat-change text-secondary-custom"><i class="bi bi-bank"></i> Total funds</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-wallet2 text-warning-custom"></i></div>
            <div class="stat-label">Total Contributions</div>
            <div class="stat-value">KES {{ number_format($totalContributions, 0) }}</div>
            <div class="stat-change text-success-custom"><i class="bi bi-arrow-up-short"></i> All time</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light"><i class="bi bi-bank2 text-danger-custom"></i></div>
            <div class="stat-label">Total Loans</div>
            <div class="stat-value">KES {{ number_format($totalLoans, 0) }}</div>
            <div class="stat-change text-secondary-custom"><i class="bi bi-arrow-down-short text-danger-custom"></i> Disbursed</div>
        </div>
    </div>
</div>

<!-- SECOND ROW -->
<div class="row g-3 mb-4">

    <!-- PENDING APPROVALS -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header-custom">
                <span class="card-title-custom">Pending Approvals</span>
                @if($pendingMembers->count() > 0)
                    <span class="badge-custom badge-warning">{{ $pendingMembers->count() }} waiting</span>
                @endif
            </div>
            <div class="card-body-custom">
                @forelse($pendingMembers as $member)
                    <div class="member-list-row">
                        <div class="member-avatar avatar-orange">{{ strtoupper(substr($member->name,0,2)) }}</div>
                        <div class="member-list-info">
                            <div class="font-semibold" style="font-size:13px">{{ $member->name }}</div>
                            <div class="text-muted-custom" style="font-size:11px">{{ $member->email }}</div>
                        </div>
                        <div class="member-list-actions">
                            <form method="POST" action="{{ route('admin.members.approve', $member->id) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="action-btn action-btn-approve" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.members.reject', $member->id) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="action-btn action-btn-reject" title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:30px 0">
                        <i class="bi bi-check-circle" style="color:#16a34a;opacity:1;font-size:32px;display:block;margin-bottom:8px"></i>
                        <h3>No pending approvals</h3>
                    </div>
                @endforelse
                @if($pendingMembers->count() > 0)
                    <a href="{{ route('admin.members') }}" class="btn-outline-custom btn-sm w-100 justify-content-center mt-3">
                        View all members <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- ACTIVE MEMBERS -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header-custom">
                <span class="card-title-custom">Active Members</span>
                <a href="{{ route('admin.members') }}" class="btn-outline-custom btn-sm">Manage</a>
            </div>
            <div class="card-body-custom">
                @php $avatarColors = ['avatar-blue','avatar-green','avatar-orange','avatar-teal','avatar-purple','avatar-red']; @endphp
                @foreach($members->take(6) as $member)
                    <div class="member-list-row">
                        <div class="member-avatar {{ $avatarColors[$loop->index % count($avatarColors)] }}">
                            {{ strtoupper(substr($member->name,0,2)) }}
                        </div>
                        <div class="member-list-info">
                            <div class="font-semibold" style="font-size:13px">{{ $member->name }}</div>
                            <div class="text-muted-custom" style="font-size:11px">{{ ucfirst($member->role) }}</div>
                        </div>
                        <span class="badge-custom badge-success">Active</span>
                    </div>
                @endforeach
                @if($members->count() > 6)
                    <div class="text-center text-muted-custom" style="font-size:12px;padding-top:8px">
                        +{{ $members->count() - 6 }} more members
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header-custom">
                <span class="card-title-custom">Recent Activity</span>
                <a href="{{ route('admin.audit') }}" class="btn-outline-custom btn-sm">Full Log</a>
            </div>
            <div class="card-body-custom">
                @forelse($recentActivities as $log)
                    <div class="activity-item">
                        <div class="activity-dot
                            {{ str_contains($log->action,'contribution') ? 'dot-green' :
                               (str_contains($log->action,'loan') ? 'dot-orange' :
                               (str_contains($log->action,'member') ? 'dot-blue' : 'dot-grey')) }}">
                        </div>
                        <div>
                            <div class="activity-text">{{ $log->description }}</div>
                            <div class="activity-meta">
                                {{ $log->user->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:30px 0">
                        <i class="bi bi-activity"></i>
                        <h3>No recent activity</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- CHARTS ROW -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Monthly Contributions</span>
                <span class="text-muted-custom" style="font-size:12px">Last 6 months</span>
            </div>
            <div class="card-body-custom">
                <canvas id="contributionChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Payment Methods</span>
            </div>
            <div class="card-body-custom d-flex align-items-center justify-content-center">
                <canvas id="paymentChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Contribution bar chart ────────────────────────────────────────
const chartLabels  = @json($chartLabels);
const chartAmounts = @json($chartAmounts);

new Chart(document.getElementById('contributionChart'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Contributions (KES)',
            data: chartAmounts,
            backgroundColor: '#1d4ed8',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                grid: { color: '#f1f5f9' },
                ticks: {
                    font: { size: 11 },
                    callback: val => 'KES ' + val.toLocaleString()
                }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// ── Payment methods doughnut ──────────────────────────────────────
const paymentMethods = @json($paymentMethods);

new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: ['M-Pesa', 'PayPal', 'Wave', 'Cash'],
        datasets: [{
            data: [
                paymentMethods.mpesa,
                paymentMethods.paypal,
                paymentMethods.wave,
                paymentMethods.cash
            ],
            backgroundColor: ['#1d4ed8', '#16a34a', '#0891b2', '#d97706'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '72%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 11 }, padding: 12 }
            }
        }
    }
});
</script>
@endpush