<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


</head>
<body>

<div class="auth-left">
    <div class="auth-brand">
        <div class="auth-brand-icon"><i class="bi bi-coin"></i></div>
        <span class="auth-brand-name">SmartChama</span>
    </div>

    <h1 class="auth-headline">
        Your chama,<br><span>digitised.</span>
    </h1>
    <p class="auth-sub">
        Track contributions, manage loans, and keep every member informed — all in one secure platform built for Kenyan savings groups.
    </p>

    <ul class="feature-list">
        <li><i class="bi bi-check-lg"></i> Real-time contribution tracking</li>
        <li><i class="bi bi-check-lg"></i> M-Pesa & diaspora payment support</li>
        <li><i class="bi bi-check-lg"></i> Loan management with approval workflow</li>
        <li><i class="bi bi-check-lg"></i> Automated financial reports & audit logs</li>
    </ul>
</div>

<div class="auth-right">
    <div class="auth-form-container">
        <h2 class="auth-form-title">Welcome back</h2>
        <p class="auth-form-sub">Sign in to your chama account</p>

        @if(session('status'))
            <div class="session-error" style="background:#f0fdf4;border-color:#bbf7d0;color:#16a34a">
                <i class="bi bi-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="session-error">
                <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="you@example.com" required autofocus>
                </div>
                @error('email')
                    <div class="error-msg"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password"
                           class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="Enter your password" required>
                </div>
                @error('password')
                    <div class="error-msg"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </div>
    </div>
</div>

</body>
</html>