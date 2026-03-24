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

<div class="auth-layout">

    <!-- LEFT PANEL -->
    <div class="auth-panel-left">
        <div class="auth-brand">
            <div class="brand-icon"><i class="bi bi-coin"></i></div>
            <span class="brand-name">SmartChama</span>
        </div>

        <h1 class="auth-headline">
            Your chama,<br><span>digitised.</span>
        </h1>

        <p class="auth-sub-text">
            Track contributions, manage loans, and keep every member informed — all in one secure platform built for Kenyan savings groups.
        </p>

        <ul class="auth-feature-list">
            <li><span class="feat-icon"><i class="bi bi-check"></i></span> Real-time contribution tracking</li>
            <li><span class="feat-icon"><i class="bi bi-check"></i></span> M-Pesa & diaspora payment support</li>
            <li><span class="feat-icon"><i class="bi bi-check"></i></span> Loan management with approval workflow</li>
            <li><span class="feat-icon"><i class="bi bi-check"></i></span> Automated reports & audit logs</li>
        </ul>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel-right">
        <div class="auth-form-box">

            <h2 class="auth-form-title">Welcome back</h2>
            <p class="auth-form-sub">Sign in to your chama account</p>

            @if(session('status'))
                <div class="alert-custom alert-success">
                    <i class="bi bi-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-custom alert-danger">
                    <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-form-group">
                    <label class="auth-form-label">Email Address</label>
                    <div class="auth-input-group">
                        <i class="bi bi-envelope auth-input-icon"></i>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                               placeholder="you@example.com" required autofocus>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Password</label>
                    <div class="auth-input-group">
                        <i class="bi bi-lock auth-input-icon"></i>
                        <input type="password" name="password"
                               class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                               placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="auth-form-row">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>

                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-forgot-link">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="auth-btn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="auth-switch-link">
                Don't have an account?
                <a href="{{ route('register') }}">Register</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
