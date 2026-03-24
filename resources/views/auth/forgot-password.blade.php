<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-layout">

    <!-- LEFT PANEL -->
    <div class="auth-panel-left">
        <div class="auth-brand">
            <div class="brand-icon"><i class="bi bi-coin"></i></div>
            <span class="brand-name">SmartChama</span>
        </div>
        <h1 class="auth-headline">
            Reset your<br><span>password.</span>
        </h1>
        <p class="auth-sub-text">
            Enter the email address linked to your chama account and we'll send you a link to reset your password.
        </p>
        <ul class="auth-feature-list">
            <li><span class="feat-icon"><i class="bi bi-shield-check"></i></span> Secure password reset link</li>
            <li><span class="feat-icon"><i class="bi bi-clock"></i></span> Link expires in 15 minutes</li>
            <li><span class="feat-icon"><i class="bi bi-envelope-check"></i></span> Check your inbox </li>
        </ul>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel-right">
        <div class="auth-form-box">

            <h2 class="auth-form-title">Forgot your password?</h2>
            <p class="auth-form-sub">We'll email you a reset link</p>

            @if(session('status'))
                <div class="alert-custom alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="auth-form-group">
                    <label class="auth-form-label">Email Address</label>
                    <div class="auth-input-group">
                        <i class="bi bi-envelope auth-input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                               placeholder="you@example.com" required autofocus>
                    </div>
                    @error('email')
                        <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="auth-btn" style="margin-top:8px">
                    <i class="bi bi-send"></i> Send Reset Link
                </button>
            </form>

            <div class="auth-switch-link" style="margin-top:20px">
                Remember your password? <a href="{{ route('login') }}">Sign in</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>