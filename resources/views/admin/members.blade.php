@extends('layouts.app')
@section('title','Manage Members')
@section('page-title','Members')
@section('page-subtitle','Manage group members and approvals')

@section('content')

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-people text-primary-custom"></i></div>
            <div class="stat-label">Total Members</div>
            <div class="stat-value">{{ $members->count() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-person-check text-success-custom"></i></div>
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $members->where('status','active')->count() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-clock text-warning-custom"></i></div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $members->where('status','pending')->count() }}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light"><i class="bi bi-person-x text-danger-custom"></i></div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ $members->where('status','rejected')->count() }}</div>
        </div>
    </div>
</div>

<!-- PENDING APPROVALS -->
@if($members->where('status','pending')->count() > 0)
<div class="card mb-4">
    <div class="card-header-custom">
        <span class="card-title-custom">Pending Approvals</span>
        <span class="badge-custom badge-warning">{{ $members->where('status','pending')->count() }} waiting</span>
    </div>
    <table class="table-custom">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @foreach($members->where('status','pending') as $member)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="member-avatar avatar-orange">{{ strtoupper(substr($member->name,0,2)) }}</div>
                        <div class="font-semibold" style="font-size:13px">{{ $member->name }}</div>
                    </div>
                </td>
                <td style="font-size:13px">{{ $member->email }}</td>
                <td class="font-mono" style="font-size:13px">{{ $member->phone ?? '—' }}</td>
                <td class="text-muted-custom" style="font-size:12px">{{ $member->created_at->format('d M Y') }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.members.approve', $member->id) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn-primary-custom btn-sm">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.members.reject', $member->id) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn-danger-custom btn-sm">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- ALL MEMBERS TABLE -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">All Members</span>
        <input type="text" id="memberSearch" placeholder="Search members..."
               class="form-control-custom" style="width:200px;font-size:12px"
               oninput="filterMembers()">
    </div>
    <table class="table-custom" id="membersTable">
        <thead>
            <tr>
                <th>Member</th><th>Role</th><th>Phone</th><th>Joined</th><th>Contributed</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $avatarColors = ['avatar-blue','avatar-green','avatar-orange','avatar-teal','avatar-purple','avatar-red']; @endphp
            @forelse($members as $member)
            <tr class="member-search-row">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="member-avatar {{ $avatarColors[$loop->index % count($avatarColors)] }}">
                            {{ strtoupper(substr($member->name,0,2)) }}
                        </div>
                        <div>
                            <div class="font-semibold" style="font-size:13px">{{ $member->name }}</div>
                            <div class="text-muted-custom" style="font-size:11px">{{ $member->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <select onchange="updateRole({{ $member->id }}, this.value)" class="form-control-custom" style="font-size:12px;padding:5px 10px;width:auto">
                        <option value="member"    {{ $member->role==='member'    ? 'selected':'' }}>Member</option>
                        <option value="treasurer" {{ $member->role==='treasurer' ? 'selected':'' }}>Treasurer</option>
                        <option value="admin"     {{ $member->role==='admin'     ? 'selected':'' }}>Admin</option>
                    </select>
                </td>
                <td class="font-mono" style="font-size:12.5px">{{ $member->phone ?? '—' }}</td>
                <td class="text-muted-custom" style="font-size:12px">{{ $member->created_at->format('d M Y') }}</td>
                <td class="font-bold">KES {{ number_format($member->contributions()->where('status','completed')->sum('amount'), 0) }}</td>
                <td>
                    @if($member->status==='active')    <span class="badge-custom badge-success">Active</span>
                    @elseif($member->status==='pending') <span class="badge-custom badge-warning">Pending</span>
                    @else <span class="badge-custom badge-danger">Rejected</span>
                    @endif
                </td>
                <td>
                    @if($member->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.members.toggle', $member->id) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn-outline-custom btn-sm {{ $member->status==='active' ? 'text-danger-custom' : '' }}">
                            {{ $member->status==='active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted-custom" style="padding:30px">No members found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
function filterMembers() {
    const q = document.getElementById('memberSearch').value.toLowerCase();
    document.querySelectorAll('.member-search-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function updateRole(memberId, role) {
    fetch(`/admin/members/${memberId}/role`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ role })
    }).then(r => r.json()).then(d => {
        if (d.success) console.log('Role updated to ' + role);
    });
}
</script>
@endpush