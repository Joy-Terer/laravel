@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Full system activity trail')

@section('content')

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-activity text-primary-custom"></i></div>
            <div class="stat-label">Total Actions</div>
            <div class="stat-value">{{ $logs->total() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-wallet2 text-success-custom"></i></div>
            <div class="stat-label">Contributions</div>
            <div class="stat-value">{{ $logs->getCollection()->filter(fn($l) => str_contains($l->action,'contribution'))->count() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-bank text-warning-custom"></i></div>
            <div class="stat-label">Loan Actions</div>
            <div class="stat-value">{{ $logs->getCollection()->filter(fn($l) => str_contains($l->action,'loan'))->count() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-light"><i class="bi bi-people text-info-custom"></i></div>
            <div class="stat-label">Member Actions</div>
            <div class="stat-value">{{ $logs->getCollection()->filter(fn($l) => str_contains($l->action,'member'))->count() }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">System Audit Trail</span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit.export') }}" class="btn-outline-custom btn-sm">
                <i class="bi bi-download"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="table-filters">
        <form method="GET" class="filters-form">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control-custom filter-input"
                   placeholder="Search action or description...">
            <input type="date" name="from" value="{{ request('from') }}"
                   class="form-control-custom filter-input">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="form-control-custom filter-input">
            <select name="action" class="form-control-custom filter-input">
                <option value="">All Actions</option>
                <option value="contribution" {{ request('action')==='contribution' ? 'selected':'' }}>Contributions</option>
                <option value="loan"         {{ request('action')==='loan'         ? 'selected':'' }}>Loans</option>
                <option value="member"       {{ request('action')==='member'       ? 'selected':'' }}>Members</option>
                <option value="auth"         {{ request('action')==='auth'         ? 'selected':'' }}>Auth</option>
            </select>
            <button type="submit" class="btn-primary-custom btn-sm">
                <i class="bi bi-funnel"></i> Filter
            </button>
            @if(request()->anyFilled(['search','from','to','action']))
                <a href="{{ route('admin.audit') }}" class="btn-outline-custom btn-sm">Clear</a>
            @endif
        </form>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>#</th>
                <th>Date & Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="font-mono text-muted-custom" style="font-size:11px">#{{ $log->id }}</td>
                <td>
                    <div class="font-semibold" style="font-size:13px">{{ $log->created_at->format('d M Y') }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $log->created_at->format('h:i:s A') }}</div>
                </td>
                <td>
                    @if($log->user)
                        <div class="d-flex align-items-center gap-2">
                            <div class="member-avatar avatar-sm avatar-blue">
                                {{ strtoupper(substr($log->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:12.5px;font-weight:600">{{ $log->user->name }}</div>
                                <div class="text-muted-custom" style="font-size:11px">{{ ucfirst($log->user->role) }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-muted-custom" style="font-size:12px">System</span>
                    @endif
                </td>
                <td>
                    @php
                        $actionType = str_contains($log->action,'contribution') ? 'success'
                            : (str_contains($log->action,'loan') ? 'warning'
                            : (str_contains($log->action,'member') ? 'primary'
                            : (str_contains($log->action,'auth') ? 'info' : 'grey')));
                    @endphp
                    <span class="badge-custom badge-{{ $actionType }}">
                        {{ str_replace('.', ' ', $log->action) }}
                    </span>
                </td>
                <td style="font-size:13px;max-width:280px">{{ $log->description }}</td>
                <td class="font-mono text-muted-custom" style="font-size:11.5px">
                    {{ $log->ip_address ?? '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-shield-check"></i>
                        <h3>No audit logs yet</h3>
                        <p>System actions will appear here</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-pagination">{{ $logs->links() }}</div>
</div>

@endsection