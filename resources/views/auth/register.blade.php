<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
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
            Join your<br><span>chama group</span><br>online.
        </h1>

        <p class="auth-sub-text">
            Register in minutes and start tracking your contributions and loans digitally.
        </p>

        <div class="auth-steps">
            <div class="auth-step">
                <div class="auth-step-num">1</div>
                <div class="auth-step-text">
                    <strong>Fill in your details</strong>
                    Use your M-Pesa phone number for payments.
                </div>
            </div>

            <div class="auth-step">
                <div class="auth-step-num">2</div>
                <div class="auth-step-text">
                    <strong>Wait for approval</strong>
                    Your admin will activate your account.
                </div>
            </div>

            <div class="auth-step">
                <div class="auth-step-num">3</div>
                <div class="auth-step-text">
                    <strong>Start contributing</strong>
                    Log in and begin.
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel-right">
        <div class="auth-form-box auth-form-box-wide">

            <h2 class="auth-form-title">Create your account</h2>
            <p class="auth-form-sub">All fields are required</p>

            <div class="alert-custom alert-info">
                <i class="bi bi-info-circle"></i>
                Your account will be reviewed before activation.
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="auth-form-row-2">

                    <div class="auth-form-group">
                        <label class="auth-form-label">First Name</label>
                        <input type="text" name="first_name"
                               class="auth-input"
                               value="{{ old('first_name') }}" required>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Last Name</label>
                        <input type="text" name="last_name"
                               class="auth-input"
                               value="{{ old('last_name') }}" required>
                    </div>

                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Email</label>
                    <input type="email" name="email"
                           class="auth-input"
                           value="{{ old('email') }}" required>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Phone</label>
                    <input type="text" name="phone"
                           class="auth-input"
                           placeholder="0712345678" required>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Group Code</label>
                    <input type="text" name="chama_code"
                           class="auth-input"
                           style="text-transform:uppercase" required>
                </div>

                <div class="auth-form-row-2">

                    <div class="auth-form-group">
                        <label class="auth-form-label">Password</label>
                        <input type="password" name="password"
                               class="auth-input" required>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="auth-input" required>
                    </div>

                </div>

                <button type="submit" class="auth-btn">
                    <i class="bi bi-person-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-switch-link">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </div>

        </div>
    </div>

</div>
</body>
</html>
