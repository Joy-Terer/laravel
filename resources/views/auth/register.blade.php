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

<div class="auth-left">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-coin"></i></div>
        <span class="brand-name">SmartChama</span>
    </div>
    <h1 class="auth-headline">Join your<br><span>chama group</span><br>online.</h1>
    <p class="auth-sub">Register in minutes and start tracking your contributions and loans digitally.</p>

    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text"><strong>Fill in your details</strong>Use your M-Pesa phone number for payments.</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text"><strong>Wait for approval</strong>Your group admin will review and activate your account.</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text"><strong>Start contributing</strong>Log in and make your first contribution instantly.</div>
        </div>
    </div>
</div>

<div class="auth-right">
    <div class="form-container">
        <h2 class="form-title">Create your account</h2>
        <p class="form-sub">All fields are required unless marked optional</p>

        <div class="pending-note">
            <i class="bi bi-info-circle-fill" style="margin-top:1px;flex-shrink:0"></i>
            <span>After registering, your account will be reviewed by the group administrator before you can log in.</span>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                               class="form-input {{ $errors->has('first_name') ? 'error' : '' }}"
                               placeholder="Joy" required>
                    </div>
                    @error('first_name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                               class="form-input {{ $errors->has('last_name') ? 'error' : '' }}"
                               placeholder="Tracy" required>
                    </div>
                    @error('last_name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="you@example.com" required>
                </div>
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number (M-Pesa)</label>
                <div class="input-wrap">
                    <i class="bi bi-phone input-icon"></i>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="form-input {{ $errors->has('phone') ? 'error' : '' }}"
                           placeholder="0712345678" required>
                </div>
                <div class="hint">Use the phone number registered with M-Pesa</div>
                @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Chama Group Code</label>
                <div class="input-wrap">
                    <i class="bi bi-key input-icon"></i>
                    <input type="text" name="chama_code" value="{{ old('chama_code') }}"
                           class="form-input {{ $errors->has('chama_code') ? 'error' : '' }}"
                           placeholder="e.g. WAMBUA01" required style="text-transform:uppercase">
                </div>
                <div class="hint">Get this code from your group administrator</div>
                @error('chama_code')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password"
                               class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                               placeholder="Min. 8 characters" required>
                    </div>
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password_confirmation"
                               class="form-input" placeholder="Repeat password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-person-plus"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</div>

</body>
</html>