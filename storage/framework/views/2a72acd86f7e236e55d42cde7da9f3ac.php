<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f1f5f9; }
        .error-page { text-align:center; max-width:480px; padding:40px 24px; }
        .error-code { font-size:96px; font-weight:800; color:#e2e8f0; line-height:1; letter-spacing:-4px; margin-bottom:8px; }
        .error-icon { font-size:52px; color:#1d4ed8; margin-bottom:20px; display:block; }
        .error-title { font-size:24px; font-weight:700; color:#0f172a; margin-bottom:10px; }
        .error-desc { font-size:14px; color:#64748b; margin-bottom:30px; line-height:1.7; }
        .error-actions { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
    </style>
</head>
<body>
<div class="error-page">
    <div class="error-code">404</div>
    <i class="bi bi-search error-icon"></i>
    <h1 class="error-title">Page Not Found</h1>
    <p class="error-desc">
        The page you're looking for doesn't exist or may have been moved.
        Check the URL or go back to your dashboard.
    </p>
    <div class="error-actions">
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-primary-custom">
                <i class="bi bi-grid-1x2"></i> Go to Dashboard
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="btn-primary-custom">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        <?php endif; ?>
        <a href="javascript:history.back()" class="btn-outline-custom">
            <i class="bi bi-arrow-left"></i> Go Back
        </a>
    </div>
</div>
</body>
</html><?php /**PATH D:\projects\jt-assignment\resources\views/errors/404.blade.php ENDPATH**/ ?>