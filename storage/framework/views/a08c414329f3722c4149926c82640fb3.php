
<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama'); ?>

<?php $__env->startSection('content'); ?>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light"><i class="bi bi-people text-primary-custom"></i></div>
            <div class="stat-label">Total Members</div>
            <div class="stat-value"><?php echo e($members->count()); ?></div>
            <div class="stat-change <?php echo e($pendingMembers->count() > 0 ? 'text-warning-custom' : 'text-success-custom'); ?>">
                <?php if($pendingMembers->count() > 0): ?>
                    <i class="bi bi-clock"></i> <?php echo e($pendingMembers->count()); ?> pending approval
                <?php else: ?>
                    <i class="bi bi-check-circle"></i> All approved
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light"><i class="bi bi-cash-stack text-success-custom"></i></div>
            <div class="stat-label">Group Balance</div>
            <div class="stat-value">KES <?php echo e(number_format($chama->balance ?? 0, 0)); ?></div>
            <div class="stat-change text-secondary-custom"><i class="bi bi-bank"></i> Total funds</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light"><i class="bi bi-wallet2 text-warning-custom"></i></div>
            <div class="stat-label">Total Contributions</div>
            <div class="stat-value">KES <?php echo e(number_format($totalContributions, 0)); ?></div>
            <div class="stat-change text-success-custom"><i class="bi bi-arrow-up-short"></i> All time</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-light"><i class="bi bi-bank2 text-danger-custom"></i></div>
            <div class="stat-label">Total Loans</div>
            <div class="stat-value">KES <?php echo e(number_format($totalLoans, 0)); ?></div>
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
                <?php if($pendingMembers->count() > 0): ?>
                    <span class="badge-custom badge-warning"><?php echo e($pendingMembers->count()); ?> waiting</span>
                <?php endif; ?>
            </div>
            <div class="card-body-custom">
                <?php $__empty_1 = true; $__currentLoopData = $pendingMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="member-list-row">
                        <div class="member-avatar avatar-orange"><?php echo e(strtoupper(substr($member->name,0,2))); ?></div>
                        <div class="member-list-info">
                            <div class="font-semibold" style="font-size:13px"><?php echo e($member->name); ?></div>
                            <div class="text-muted-custom" style="font-size:11px"><?php echo e($member->email); ?></div>
                        </div>
                        <div class="member-list-actions">
                            <form method="POST" action="<?php echo e(route('admin.members.approve', $member->id)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit" class="action-btn action-btn-approve" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.members.reject', $member->id)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit" class="action-btn action-btn-reject" title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state" style="padding:30px 0">
                        <i class="bi bi-check-circle" style="color:#16a34a;opacity:1;font-size:32px;display:block;margin-bottom:8px"></i>
                        <h3>No pending approvals</h3>
                    </div>
                <?php endif; ?>
                <?php if($pendingMembers->count() > 0): ?>
                    <a href="<?php echo e(route('admin.members')); ?>" class="btn-outline-custom btn-sm w-100 justify-content-center mt-3">
                        View all members <i class="bi bi-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ACTIVE MEMBERS -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header-custom">
                <span class="card-title-custom">Active Members</span>
                <a href="<?php echo e(route('admin.members')); ?>" class="btn-outline-custom btn-sm">Manage</a>
            </div>
            <div class="card-body-custom">
                <?php $avatarColors = ['avatar-blue','avatar-green','avatar-orange','avatar-teal','avatar-purple','avatar-red']; ?>
                <?php $__currentLoopData = $members->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="member-list-row">
                        <div class="member-avatar <?php echo e($avatarColors[$loop->index % count($avatarColors)]); ?>">
                            <?php echo e(strtoupper(substr($member->name,0,2))); ?>

                        </div>
                        <div class="member-list-info">
                            <div class="font-semibold" style="font-size:13px"><?php echo e($member->name); ?></div>
                            <div class="text-muted-custom" style="font-size:11px"><?php echo e(ucfirst($member->role)); ?></div>
                        </div>
                        <span class="badge-custom badge-success">Active</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($members->count() > 6): ?>
                    <div class="text-center text-muted-custom" style="font-size:12px;padding-top:8px">
                        +<?php echo e($members->count() - 6); ?> more members
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header-custom">
                <span class="card-title-custom">Recent Activity</span>
                <a href="<?php echo e(route('admin.audit')); ?>" class="btn-outline-custom btn-sm">Full Log</a>
            </div>
            <div class="card-body-custom">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="activity-item">
                        <div class="activity-dot
                            <?php echo e(str_contains($log->action,'contribution') ? 'dot-green' :
                               (str_contains($log->action,'loan') ? 'dot-orange' :
                               (str_contains($log->action,'member') ? 'dot-blue' : 'dot-grey'))); ?>">
                        </div>
                        <div>
                            <div class="activity-text"><?php echo e($log->description); ?></div>
                            <div class="activity-meta">
                                <?php echo e($log->user->name ?? 'System'); ?> · <?php echo e($log->created_at->diffForHumans()); ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state" style="padding:30px 0">
                        <i class="bi bi-activity"></i>
                        <h3>No recent activity</h3>
                    </div>
                <?php endif; ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Contribution bar chart ────────────────────────────────────────
const chartLabels  = <?php echo json_encode($chartLabels, 15, 512) ?>;
const chartAmounts = <?php echo json_encode($chartAmounts, 15, 512) ?>;

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
const paymentMethods = <?php echo json_encode($paymentMethods, 15, 512) ?>;

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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\jt-assignment\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>