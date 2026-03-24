<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Smart Chama'); ?> — Smart Chama</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <link rel="stylesheet" href="/css/app.css">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo e(route('dashboard')); ?>" class="brand-logo">
            <div class="brand-icon"><i class="bi bi-coin"></i></div>
            <div>
                <div class="brand-name">SmartChama</div>
                <div class="brand-tagline">Savings Platform</div>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <div class="nav-item">
            <a href="<?php echo e(route('dashboard')); ?>"
               class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </div>

        <div class="nav-item">
            <a href="<?php echo e(route('contributions.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('contributions.*') ? 'active' : ''); ?>">
                <i class="bi bi-wallet2"></i> Contributions
            </a>
        </div>

        <div class="nav-item">
            <a href="<?php echo e(route('loans.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('loans.*') ? 'active' : ''); ?>">
                <i class="bi bi-bank"></i> Loans
            </a>
        </div>

        <?php if(auth()->user()->role === 'treasurer' || auth()->user()->role === 'admin'): ?>
        <div class="nav-section-label">Management</div>

        <div class="nav-item">
            <a href="<?php echo e(route('reports.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
        </div>
        <?php endif; ?>

        <?php if(auth()->user()->role === 'admin'): ?>
        <div class="nav-section-label">Admin</div>

        <div class="nav-item">
            <a href="<?php echo e(route('admin.members')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.members') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i> Members
                <?php $pending = \App\Models\User::where('status','pending')->count(); ?>
                <?php if($pending > 0): ?>
                    <span class="nav-badge"><?php echo e($pending); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="nav-item">
            <a href="<?php echo e(route('admin.audit')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.audit') ? 'active' : ''); ?>">
                <i class="bi bi-shield-check"></i> Audit Logs
            </a>
        </div>
        <?php endif; ?>

        <div class="nav-section-label">Account</div>
 
        <div class="nav-item">
            <a href="<?php echo e(route('billing.plans')); ?>"
               class="nav-link <?php echo e(request()->routeIs('billing.*') ? 'active' : ''); ?>">
                <i class="bi bi-credit-card"></i> Plans & Billing
                <?php if(auth()->user()->chama?->isOnFreePlan()): ?>
                    <span class="nav-badge" style="background:#d97706">Free</span>
                <?php endif; ?>
            </a>
        </div>
 
    </nav>

    <div class="sidebar-user">
        <div class="tp-avatar">
            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?>

        </div>
        <div style="min-width:0">
            <div class="user-name"><?php echo e(auth()->user()->name); ?></div>
            <div class="user-role"><?php echo e(auth()->user()->role); ?></div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>

<!-- ── Main Wrapper ──────────────────────────────────────────────────── -->
<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-left">
                <h1><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                <p><?php echo $__env->yieldContent('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama'); ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <a href="<?php echo e(route('contributions.create')); ?>" class="btn-primary-custom" style="font-size:12px;padding:7px 14px">
                <i class="bi bi-plus-lg"></i> Contribute
            </a>
            <a href="#" class="topbar-btn" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="notif-dot"></span>
            </a>
            <a href="<?php echo e(route('profile.edit')); ?>" class="topbar-profile">
                <div class="tp-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></div>
                <span class="tp-name"><?php echo e(explode(' ', auth()->user()->name)[0]); ?></span>
                <i class="bi bi-chevron-down" style="font-size:10px;color:var(--text-muted)"></i>
            </a>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        
        <?php if(session('success')): ?>
        <div class="alert-custom alert-success">
            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <div class="alert-custom alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i> <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        }
    });
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\projects\jt-assignment\resources\views/layouts/app.blade.php ENDPATH**/ ?>