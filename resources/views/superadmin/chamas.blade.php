@extends('superadmin.layout')
@section('title', 'All Chamas')
@section('page-title', 'All Chamas')

@section('content')

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Chama Groups ({{ $chamas->total() }})</span>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control-custom" style="width:180px;font-size:12px"
                   placeholder="Search chamas...">
            <select name="plan" class="form-control-custom" style="width:130px;font-size:12px">
                <option value="">All Plans</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->slug }}" {{ request('plan') === $plan->slug ? 'selected' : '' }}>
                        {{ $plan->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary-custom btn-sm">
                <i class="bi bi-funnel"></i> Filter
            </button>
            @if(request()->anyFilled(['search','plan']))
                <a href="{{ route('superadmin.chamas') }}" class="btn-outline-custom btn-sm">Clear</a>
            @endif
        </form>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Chama</th>
                <th>Admin</th>
                <th>Plan</th>
                <th>Members</th>
                <th>Balance</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chamas as $chama)
            <tr>
                <td>
                    <div class="font-semibold" style="font-size:13px">{{ $chama->name }}</div>
                    <div class="text-muted-custom" style="font-size:11px">Code: {{ $chama->code }}</div>
                </td>
                <td style="font-size:12.5px">
                    <div class="font-semibold">{{ $chama->admin?->name ?? '—' }}</div>
                    <div class="text-muted-custom" style="font-size:11px">{{ $chama->admin?->email }}</div>
                </td>
                <td>
                    <select onchange="assignPlan({{ $chama->id }}, this.value)"
                            class="form-control-custom"
                            style="font-size:11px;padding:4px 8px;width:auto">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ $chama->plan_id === $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <span class="font-semibold">{{ $chama->members->count() }}</span>
                    <span class="text-muted-custom" style="font-size:11px">
                        / {{ $chama->plan?->max_members === -1 ? '∞' : $chama->plan?->max_members }}
                    </span>
                </td>
                <td class="font-bold">KES {{ number_format($chama->balance, 0) }}</td>
                <td class="text-muted-custom" style="font-size:12px">
                    {{ $chama->created_at->format('d M Y') }}
                </td>
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
                        <button type="submit"
                                class="btn-outline-custom btn-sm {{ $chama->is_active ? 'text-danger-custom' : '' }}">
                            {{ $chama->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <h3>No chamas found</h3>
                        <p>No chama groups have registered yet</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-pagination">{{ $chamas->links() }}</div>
</div>

@endsection

@push('scripts')
<script>
function assignPlan(chamaId, planId) {
    fetch(`/superadmin/chamas/${chamaId}/plan`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? ''
        },
        body: JSON.stringify({ plan_id: planId })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const msg = document.createElement('div');
            msg.className = 'alert-custom alert-success';
            msg.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:250px';
            msg.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Plan updated successfully</span>';
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 3000);
        }
    });
}
</script>
@endpush