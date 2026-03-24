
<?php $__env->startSection('title', 'Loans'); ?>
<?php $__env->startSection('page-title', 'Loan Management'); ?>
<?php $__env->startSection('page-subtitle', 'Your loans and repayments'); ?>

<?php $__env->startSection('content'); ?>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-bank text-warning-custom"></i>
            </div>
            <div class="stat-label">Total Borrowed</div>
            <div class="stat-value">KES <?php echo e(number_format($loans->sum('amount'), 0)); ?></div>
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
                KES <?php echo e(number_format($loans->whereIn('status', ['approved', 'active'])->sum('balance'), 0)); ?>

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
            <div class="stat-value"><?php echo e($loans->where('status', 'repaid')->count()); ?></div>
            <div class="stat-change text-success-custom">Completed</div>
        </div>
    </div>
</div>

<!-- LOANS LIST -->
<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom">My Loans</span>
        <a href="<?php echo e(route('loans.apply')); ?>" class="btn-primary-custom btn-sm">
            <i class="bi bi-plus-lg"></i> Apply for Loan
        </a>
    </div>

    <div class="card-body-custom">
        <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="loan-card">

                <!-- LOAN HEADER -->
                <div class="loan-card-header">
                    <div>
                        <div class="loan-card-amount">KES <?php echo e(number_format($loan->amount, 0)); ?></div>
                        <div class="loan-card-meta">
                            <?php echo e($loan->purpose); ?>

                            &nbsp;·&nbsp;
                            <?php echo e($loan->repayment_period); ?> month(s)
                            &nbsp;·&nbsp;
                            Applied <?php echo e($loan->created_at->format('d M Y')); ?>

                        </div>
                        <?php if($loan->decline_reason): ?>
                            <div class="loan-decline-reason">
                                <i class="bi bi-info-circle"></i> <?php echo e($loan->decline_reason); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="loan-card-status">
                        <?php if($loan->status === 'pending'): ?>
                            <span class="badge-custom badge-warning">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                        <?php elseif($loan->status === 'approved'): ?>
                            <span class="badge-custom badge-primary">
                                <i class="bi bi-check-circle"></i> Approved
                            </span>
                        <?php elseif($loan->status === 'repaid'): ?>
                            <span class="badge-custom badge-success">
                                <i class="bi bi-check-circle-fill"></i> Repaid
                            </span>
                        <?php else: ?>
                            <span class="badge-custom badge-danger">
                                <i class="bi bi-x-circle"></i> Declined
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PROGRESS BAR (approved loans only) -->
                <?php if(in_array($loan->status, ['approved', 'active'])): ?>
                    <?php $pct = $loan->amount > 0 ? round((($loan->amount - $loan->balance) / $loan->amount) * 100) : 0; ?>

                    <div class="loan-progress-section">
                        <div class="loan-progress-labels">
                            <span>Balance: <strong class="text-danger-custom">KES <?php echo e(number_format($loan->balance, 0)); ?></strong></span>
                            <span><?php echo e(100 - $pct); ?>% remaining</span>
                        </div>
                        <div class="progress-custom">
                            <div class="progress-bar-custom bg-success-bar" style="width:<?php echo e($pct); ?>%"></div>
                        </div>
                        <?php if($loan->due_date): ?>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                Due: <?php echo e($loan->due_date->format('d M Y')); ?>

                                <?php if($loan->isOverdue()): ?>
                                    <span class="text-danger-custom font-semibold"> — OVERDUE</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- REPAY BUTTON -->
                    <?php if($loan->user_id === auth()->id()): ?>
                        <button class="btn-primary-custom btn-sm"
                                onclick="document.getElementById('repay-<?php echo e($loan->id); ?>').style.display='block';this.style.display='none'">
                            <i class="bi bi-cash"></i> Make Repayment
                        </button>

                        <!-- INLINE REPAYMENT FORM -->
                        <div id="repay-<?php echo e($loan->id); ?>" class="repay-panel">
                            <div class="repay-panel-title">Make a Repayment</div>
                            <form method="POST" action="<?php echo e(route('loans.repay', $loan->id)); ?>" class="repay-form">
                                <?php echo csrf_field(); ?>
                                <div>
                                    <label class="form-label-custom">Amount (KES)</label>
                                    <input type="number" name="amount"
                                           class="form-control-custom"
                                           placeholder="e.g. 1000"
                                           min="1"
                                           max="<?php echo e($loan->balance); ?>"
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
                                            onclick="document.getElementById('repay-<?php echo e($loan->id); ?>').style.display='none';
                                                     this.closest('.loan-card').querySelector('.btn-primary-custom').style.display='inline-flex'">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- TREASURER/ADMIN APPROVE DECLINE -->
                    <?php if(in_array(auth()->user()->role, ['treasurer', 'admin']) && $loan->status === 'pending'): ?>
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="<?php echo e(route('loans.approve', $loan->id)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit" class="btn-primary-custom btn-sm">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('loans.decline', $loan->id)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit" class="btn-danger-custom btn-sm">
                                    <i class="bi bi-x-lg"></i> Decline
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">
                <i class="bi bi-bank"></i>
                <h3>No loans yet</h3>
                <p>Apply for a loan from your chama group</p>
                <a href="<?php echo e(route('loans.apply')); ?>" class="btn-primary-custom" style="margin-top:14px">
                    <i class="bi bi-plus-lg"></i> Apply for a Loan
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\jt-assignment\resources\views/loans/index.blade.php ENDPATH**/ ?>