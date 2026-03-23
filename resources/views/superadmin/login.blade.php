{{-- ============================================================
     FILE: resources/views/superadmin/login.blade.php
     ============================================================ --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Login — Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="auth-layout">
    <div class="auth-panel-left">
        <div class="auth-brand">
            <div class="brand-icon"><i class="bi bi-coin"></i></div>
            <span class="brand-name">SmartChama</span>
        </div>
        <h1 class="auth-headline">Platform<br><span>Admin Panel.</span></h1>
        <p class="auth-sub-text">Superadmin access only. Manage all chamas, subscriptions, and platform revenue.</p>
        <ul class="auth-feature-list">
            <li><span class="feat-icon"><i class="bi bi-check-lg"></i></span> Manage all chama groups</li>
            <li><span class="feat-icon"><i class="bi bi-check-lg"></i></span> View subscription revenue</li>
            <li><span class="feat-icon"><i class="bi bi-check-lg"></i></span> Control plan assignments</li>
            <li><span class="feat-icon"><i class="bi bi-check-lg"></i></span> Platform-wide audit logs</li>
        </ul>
    </div>
    <div class="auth-panel-right">
        <div class="auth-form-box">
            <h2 class="auth-form-title">Superadmin Login</h2>
            <p class="auth-form-sub">Restricted access — authorised personnel only</p>

            @if($errors->any())
                <div class="alert-custom alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.post') }}">
                @csrf
                <div class="auth-form-group">
                    <label class="auth-form-label">Email Address</label>
                    <div class="auth-input-group">
                        <i class="bi bi-envelope auth-input-icon"></i>
                        <input type="email" name="email" class="auth-input" placeholder="superadmin@smartchama.co.ke" required autofocus>
                    </div>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Password</label>
                    <div class="auth-input-group">
                        <i class="bi bi-lock auth-input-icon"></i>
                        <input type="password" name="password" class="auth-input" placeholder="Enter password" required>
                    </div>
                </div>
                <button type="submit" class="auth-btn" style="margin-top:8px">
                    <i class="bi bi-shield-lock"></i> Login as Superadmin
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>