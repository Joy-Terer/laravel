<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Chama — Smart Chama</title>
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
            Start your<br><span>chama</span><br>today.
        </h1>

        <p class="auth-sub-text">
            Register your savings group in minutes. No technical skills needed — just your chama details and M-Pesa number to get started.
        </p>

        <div class="auth-steps">
            <div class="auth-step">
                <div class="auth-step-num">1</div>
                <div class="auth-step-text">
                    <strong>Fill in chama details</strong>
                    Name, location, contribution amount and frequency.
                </div>
            </div>
            <div class="auth-step">
                <div class="auth-step-num">2</div>
                <div class="auth-step-text">
                    <strong>Add your M-Pesa number</strong>
                    Your Paybill or Till number for collecting contributions.
                </div>
            </div>
            <div class="auth-step">
                <div class="auth-step-num">3</div>
                <div class="auth-step-text">
                    <strong>Get your chama code</strong>
                    Share it with members so they can join instantly.
                </div>
            </div>
            <div class="auth-step">
                <div class="auth-step-num">4</div>
                <div class="auth-step-text">
                    <strong>14-day Premium trial</strong>
                    All features unlocked — no credit card needed.
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel-right">
        <div class="auth-form-box auth-form-box-wide" style="max-width:520px">

            <h2 class="auth-form-title">Create your chama</h2>
            <p class="auth-form-sub">Get started in under 3 minutes</p>

            @if($errors->any())
                <div class="alert-custom alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('chama.register.store') }}">
                @csrf

                <!-- STEP 1: CHAMA DETAILS -->
                <div class="reg-section">
                    <div class="reg-section-title">
                        <span class="reg-step-num">1</span>
                        Chama Details
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Chama Name *</label>
                        <div class="auth-input-group">
                            <i class="bi bi-people auth-input-icon"></i>
                            <input type="text" name="chama_name" value="{{ old('chama_name') }}"
                                   class="auth-input {{ $errors->has('chama_name') ? 'error' : '' }}"
                                   placeholder="e.g. Wambua Savings Group" required>
                        </div>
                        @error('chama_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="auth-form-row-2">
                        <div class="auth-form-group">
                            <label class="auth-form-label">Category *</label>
                            <select name="chama_category" class="auth-input {{ $errors->has('chama_category') ? 'error' : '' }}" required>
                                <option value="general" {{ old('chama_category') === 'general' ? 'selected' : '' }}>General</option>
                                <option value="women"   {{ old('chama_category') === 'women'   ? 'selected' : '' }}>Women's Group</option>
                                <option value="youth"   {{ old('chama_category') === 'youth'   ? 'selected' : '' }}>Youth Group</option>
                                <option value="investment" {{ old('chama_category') === 'investment' ? 'selected' : '' }}>Investment Club</option>
                            </select>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-form-label">Location</label>
                            <div class="auth-input-group">
                                <i class="bi bi-geo-alt auth-input-icon"></i>
                                <input type="text" name="chama_location" value="{{ old('chama_location') }}"
                                       class="auth-input" placeholder="e.g. Nairobi, Kenya">
                            </div>
                        </div>
                    </div>

                    <div class="auth-form-row-2">
                        <div class="auth-form-group">
                            <label class="auth-form-label">Contribution Amount (KES) *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-cash auth-input-icon"></i>
                                <input type="number" name="contribution_amount"
                                       value="{{ old('contribution_amount', 2000) }}"
                                       class="auth-input {{ $errors->has('contribution_amount') ? 'error' : '' }}"
                                       min="100" required>
                            </div>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-form-label">Frequency *</label>
                            <select name="contribution_frequency" class="auth-input" required>
                                <option value="monthly"   {{ old('contribution_frequency') === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                                <option value="weekly"    {{ old('contribution_frequency') === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                                <option value="quarterly" {{ old('contribution_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            </select>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Meeting Schedule (Optional)</label>
                        <div class="auth-input-group">
                            <i class="bi bi-calendar3 auth-input-icon"></i>
                            <input type="text" name="meeting_day" value="{{ old('meeting_day') }}"
                                   class="auth-input" placeholder="e.g. Every 1st Saturday of the month">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: M-PESA DETAILS -->
                <div class="reg-section">
                    <div class="reg-section-title">
                        <span class="reg-step-num">2</span>
                        M-Pesa Collection Details
                    </div>

                    <div class="alert-custom alert-info" style="margin-bottom:16px">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            Members will send contributions directly to your M-Pesa number.
                            You can add your Daraja API keys later in Settings for automatic payment tracking.
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">M-Pesa Type *</label>
                        <div style="display:flex;gap:12px">
                            <label class="mpesa-type-option" id="opt-paybill">
                                <input type="radio" name="mpesa_type" value="paybill"
                                       {{ old('mpesa_type', 'paybill') === 'paybill' ? 'checked' : '' }}
                                       onchange="toggleMpesaType('paybill')">
                                <i class="bi bi-building"></i>
                                <span>Paybill</span>
                            </label>
                            <label class="mpesa-type-option" id="opt-till">
                                <input type="radio" name="mpesa_type" value="till"
                                       {{ old('mpesa_type') === 'till' ? 'checked' : '' }}
                                       onchange="toggleMpesaType('till')">
                                <i class="bi bi-shop"></i>
                                <span>Till Number</span>
                            </label>
                        </div>
                    </div>

                    <div class="auth-form-row-2">
                        <div class="auth-form-group">
                            <label class="auth-form-label" id="shortcode-label">Paybill Number *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-phone auth-input-icon"></i>
                                <input type="text" name="mpesa_shortcode"
                                       value="{{ old('mpesa_shortcode') }}"
                                       class="auth-input {{ $errors->has('mpesa_shortcode') ? 'error' : '' }}"
                                       placeholder="e.g. 522533" required>
                            </div>
                            @error('mpesa_shortcode')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="auth-form-group" id="account-name-wrap">
                            <label class="auth-form-label">Account Name</label>
                            <div class="auth-input-group">
                                <i class="bi bi-person-badge auth-input-icon"></i>
                                <input type="text" name="mpesa_account_name"
                                       value="{{ old('mpesa_account_name') }}"
                                       class="auth-input"
                                       placeholder="e.g. Wambua Chama">
                            </div>
                        </div>
                    </div>

                    <div class="form-hint" style="margin-top:-8px;margin-bottom:12px">
                        <i class="bi bi-lock" style="font-size:11px"></i>
                        Daraja API keys are optional — add them later in Settings for automatic STK push payments.
                    </div>
                </div>

                <!-- STEP 3: ADMIN ACCOUNT -->
                <div class="reg-section">
                    <div class="reg-section-title">
                        <span class="reg-step-num">3</span>
                        Your Admin Account
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Full Name *</label>
                        <div class="auth-input-group">
                            <i class="bi bi-person auth-input-icon"></i>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                                   class="auth-input {{ $errors->has('admin_name') ? 'error' : '' }}"
                                   placeholder="Your full name" required>
                        </div>
                    </div>

                    <div class="auth-form-row-2">
                        <div class="auth-form-group">
                            <label class="auth-form-label">Email Address *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-envelope auth-input-icon"></i>
                                <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                                       class="auth-input {{ $errors->has('admin_email') ? 'error' : '' }}"
                                       placeholder="you@example.com" required>
                            </div>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-form-label">Phone (M-Pesa) *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-phone auth-input-icon"></i>
                                <input type="text" name="admin_phone" value="{{ old('admin_phone') }}"
                                       class="auth-input {{ $errors->has('admin_phone') ? 'error' : '' }}"
                                       placeholder="07XXXXXXXX" required>
                            </div>
                        </div>
                    </div>

                    <div class="auth-form-row-2">
                        <div class="auth-form-group">
                            <label class="auth-form-label">Password *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-lock auth-input-icon"></i>
                                <input type="password" name="admin_password"
                                       class="auth-input {{ $errors->has('admin_password') ? 'error' : '' }}"
                                       placeholder="Min. 8 characters" required>
                            </div>
                        </div>
                        <div class="auth-form-group">
                            <label class="auth-form-label">Confirm Password *</label>
                            <div class="auth-input-group">
                                <i class="bi bi-lock-fill auth-input-icon"></i>
                                <input type="password" name="admin_password_confirmation"
                                       class="auth-input" placeholder="Repeat password" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    <i class="bi bi-rocket-takeoff"></i> Create My Chama
                </button>

            </form>

            <div class="auth-switch-link">
                Already have a chama? <a href="{{ route('login') }}">Sign in</a>
                &nbsp;·&nbsp;
                Want to join an existing chama? <a href="{{ route('register') }}">Join here</a>
            </div>

        </div>
    </div>

</div>

@push('styles')
<style>
.reg-section { margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9; }
.reg-section:last-of-type { border-bottom: none; }
.reg-section-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 700; color: #0f172a;
    margin-bottom: 16px;
}
.reg-step-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--primary); color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.mpesa-type-option {
    flex: 1; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 12px; display: flex; align-items: center; gap: 8px;
    cursor: pointer; font-size: 13px; font-weight: 600;
    transition: border-color .15s, background .15s;
}
.mpesa-type-option:has(input:checked) {
    border-color: var(--primary); background: var(--primary-light);
    color: var(--primary);
}
.mpesa-type-option input { display: none; }
.mpesa-type-option i { font-size: 16px; }
</style>
@endpush

<script>
function toggleMpesaType(type) {
    const label = document.getElementById('shortcode-label');
    const accountWrap = document.getElementById('account-name-wrap');
    if (type === 'till') {
        label.textContent = 'Till Number *';
        accountWrap.style.display = 'none';
    } else {
        label.textContent = 'Paybill Number *';
        accountWrap.style.display = 'block';
    }
}
</script>

</body>
</html>