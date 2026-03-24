
<?php $__env->startSection('title','Manage Members'); ?>
<?php $__env->startSection('page-title','Members'); ?>
<?php $__env->startSection('page-subtitle','Manage group members and approvals'); ?>

<?php $__env->startSection('content'); ?>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-people text-primary-custom"></i></div>
            <div class="stat-label">Total Members</div>
            <div class="stat-value"><?php echo e($members->count()); ?></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-person-check text-success-custom"></i></div>
            <div class="stat-label">Active</div>
            <div class="stat-value"><?php echo e($members->where('status','active')->count()); ?></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-clock text-warning-custom"></i></div>
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?php echo e($members->where('status','pending')->count()); ?></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light"><i class="bi bi-person-x text-danger-custom"></i></div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value"><?php echo e($members->where('status','rejected')->count()); ?></div>
        </div>
    </div>
</div>

<!-- PENDING APPROVALS -->
<?php if($members->where('status','pending')->count() > 0): ?>
<div class="card mb-3">
    <div class="card-header-custom">
        <span class="card-title-custom">Pending Approvals</span>
        <span class="badge-custom badge-warning"><?php echo e($members->where('status','pending')->count()); ?> waiting</span>
    </div>
    <table class="table-custom">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $members->where('status','pending'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="member-avatar avatar-orange"><?php echo e(strtoupper(substr($member->name,0,2))); ?></div>
                        <div class="font-semibold" style="font-size:13px"><?php echo e($member->name); ?></div>
                    </div>
                </td>
                <td style="font-size:13px"><?php echo e($member->email); ?></td>
                <td class="font-mono" style="font-size:13px"><?php echo e($member->phone ?? '—'); ?></td>
                <td class="text-muted-custom" style="font-size:12px"><?php echo e($member->created_at->format('d M Y')); ?></td>
                <td>
                    <div class="d-flex gap-2">
                        <form method="POST" action="<?php echo e(route('admin.members.approve', $member->id)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <button type="submit" class="btn-primary-custom btn-sm">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.members.reject', $member->id)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <button type="submit" class="btn-danger-custom btn-sm">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

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
            <?php $avatarColors = ['avatar-blue','avatar-green','avatar-orange','avatar-teal','avatar-purple','avatar-red']; ?>
            <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="member-search-row">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="member-avatar <?php echo e($avatarColors[$loop->index % count($avatarColors)]); ?>">
                            <?php echo e(strtoupper(substr($member->name,0,2))); ?>

                        </div>
                        <div>
                            <div class="font-semibold" style="font-size:13px"><?php echo e($member->name); ?></div>
                            <div class="text-muted-custom" style="font-size:11px"><?php echo e($member->email); ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <select onchange="updateRole(<?php echo e($member->id); ?>, this.value)" class="form-control-custom" style="font-size:12px;padding:5px 10px;width:auto">
                        <option value="member"    <?php echo e($member->role==='member'    ? 'selected':''); ?>>Member</option>
                        <option value="treasurer" <?php echo e($member->role==='treasurer' ? 'selected':''); ?>>Treasurer</option>
                        <option value="admin"     <?php echo e($member->role==='admin'     ? 'selected':''); ?>>Admin</option>
                    </select>
                </td>
                <td class="font-mono" style="font-size:12.5px"><?php echo e($member->phone ?? '—'); ?></td>
                <td class="text-muted-custom" style="font-size:12px"><?php echo e($member->created_at->format('d M Y')); ?></td>
                <td class="font-bold">KES <?php echo e(number_format($member->contributions()->where('status','completed')->sum('amount'), 0)); ?></td>
                <td>
                    <?php if($member->status==='active'): ?>    <span class="badge-custom badge-success">Active</span>
                    <?php elseif($member->status==='pending'): ?> <span class="badge-custom badge-warning">Pending</span>
                    <?php else: ?> <span class="badge-custom badge-danger">Rejected</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($member->id !== auth()->id()): ?>
                    <form method="POST" action="<?php echo e(route('admin.members.toggle', $member->id)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <button type="submit" class="btn-outline-custom btn-sm <?php echo e($member->status==='active' ? 'text-danger-custom' : ''); ?>">
                            <?php echo e($member->status==='active' ? 'Deactivate' : 'Activate'); ?>

                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center text-muted-custom" style="padding:30px">No members found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\jt-assignment\resources\views/admin/members.blade.php ENDPATH**/ ?>