@extends('superadmin.layout')
@section('title', 'Platform Dashboard')
@section('page-title', 'Platform Dashboard')

@section('content')

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-people text-primary-custom"></i></div>
            <div class="stat-label">Total Chamas</div>
            <div class="stat-value">{{ $totalChamas }}</div>
            <div class="stat-change text-secondary-custom">All groups</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-person-check text-success-custom"></i></div>
            <div class="stat-label">Total Members</div>
            <div class="stat-value">{{ $totalMembers }}</div>
            <div class="stat-change text-success-custom">Platform-wide</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-cash-stack text-warning-custom"></i></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">KES {{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-change text-success-custom"><i class="bi bi-arrow-up-short"></i> All time</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-light"><i class="bi bi-credit-card text-info-custom"></i></div>
            <div class="stat-label">Active Subscriptions</div>
            <div class="stat-value">{{ $activeSubscriptions }}</div>
            <div class="stat-change text-secondary-custom">Paying customers</div>
        </div>
    </div>
</div>

<!-- CHARTS + PLAN STATS -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Monthly Revenue</span>
                <span class="text-muted-custom" style="font-size:12px">Last 6 months</span>
            </div>
            <div class="card-body-custom">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Plan Distribution</span>
            </div>
            <div class="card-body-custom">
                @foreach($planStats as $plan)
                <div style="margin-bottom:12px">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                        <span class="font-semibold">{{ $plan->name }}</span>
                        <span class="text-muted-custom">{{ $plan->subscriptions_count }} active</span>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-bar-custom bg-primary-bar"
                             style="width:{{ $totalChamas > 0 ? round(($plan->subscriptions_count/$totalChamas)*100) : 0 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- RECENT CHAMAS -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">Recent Chamas</span>
        <a href="{{ route('superadmin.chamas') }}" class="btn-outline-custom btn-sm">View all</a>
    </div>
    <table class="table-custom">
        <thead>
            <tr><th>Chama</th><th>Plan</th><th>Members</th><th>Balance</th><th>Joined</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
            @forelse($recentChamas as $chama)
            <tr>
                <td>
                    <div class="font-semibold" style="font-size:13px">{{ $chama->name }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $chama->code }}</div>
                </td>
                <td>
                    <span class="badge-custom {{ $chama->plan?->slug === 'free' ? 'badge-grey' : 'badge-primary' }}">
                        {{ $chama->plan?->name ?? 'No Plan' }}
                    </span>
                </td>
                <td>{{ $chama->members->count() }}</td>
                <td class="font-bold">KES {{ number_format($chama->balance, 0) }}</td>
                <td class="text-muted-custom" style="font-size:12px">{{ $chama->created_at->format('d M Y') }}</td>
                <td>
                    @if($chama->is_active)
                        <span class="badge-custom badge-success">Active</span>
                    @else
                        <span class="badge-custom badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('superadmin.chamas.toggle', $chama->id) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn-outline-custom btn-sm">
                            {{ $chama->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted-custom" style="padding:30px">No chamas yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
const months  = @json($monthlyRevenue->pluck('month'));
const revenue = @json($monthlyRevenue->pluck('revenue'));

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Revenue (KES)',
            data: revenue,
            backgroundColor: '#7c3aed',
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
                ticks: { font: { size: 11 }, callback: val => 'KES ' + val.toLocaleString() }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush