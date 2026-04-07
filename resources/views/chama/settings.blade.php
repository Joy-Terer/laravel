@extends('layouts.app')
@section('title', 'Chama Settings')
@section('page-title', 'Chama Settings')
@section('page-subtitle', 'Manage your chama details and M-Pesa configuration')

@section('content')

<div class="row g-3">

    <!-- LEFT: Chama Info Card -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body-custom text-center" style="padding:32px 20px">
                <div style="width:72px;height:72px;border-radius:16px;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 14px">
                    💰
                </div>
                <div style="font-size:18px;font-weight:800;color:var(--text-primary)">{{ $chama->name }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px">{{ ucfirst($chama->category ?? 'General') }} Group</div>
                <div style="margin-top:12px">
                    <span class="badge-custom {{ $chama->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $chama->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- CHAMA CODE CARD -->
        <div class="card mb-3">
            <div class="card-header-custom">
                <span class="card-title-custom">Chama Code</span>
            </div>
            <div class="card-body-custom text-center" style="padding:24px">
                <div style="font-family:var(--font-mono);font-size:28px;font-weight:800;letter-spacing:4px;color:var(--primary);background:var(--primary-light);padding:16px;border-radius:10px;margin-bottom:12px">
                    {{ $chama->code }}
                </div>
                <p style="font-size:12px;color:var(--text-muted);margin-bottom:16px">
                    Share this code with members so they can join your chama when registering.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button onclick="copyCode('{{ $chama->code }}')" class="btn-primary-custom btn-sm">
                        <i class="bi bi-clipboard"></i> Copy Code
                    </button>
                    <form method="POST" action="{{ route('chama.regenerate-code') }}">
                        @csrf
                        <button type="submit" class="btn-outline-custom btn-sm"
                                onclick="return confirm('Are you sure? Old members will not be affected but new members will need the new code.')">
                            <i class="bi bi-arrow-repeat"></i> New Code
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- M-PESA STATUS CARD -->
        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">M-Pesa Status</span>
            </div>
            <div class="card-body-custom">
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid #f1f5f9">
                    <span class="text-muted-custom">Type</span>
                    <span class="font-semibold">{{ $chama->mpesa_type === 'till' ? 'Till Number' : 'Paybill' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid #f1f5f9">
                    <span class="text-muted-custom">Shortcode</span>
                    <span class="font-mono font-semibold">{{ $chama->mpesa_shortcode ?? 'Not set' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid #f1f5f9">
                    <span class="text-muted-custom">API Keys</span>
                    @if($chama->mpesa_consumer_key)
                        <span class="badge-custom badge-success"><i class="bi bi-check-circle"></i> Configured</span>
                    @else
                        <span class="badge-custom badge-warning"><i class="bi bi-exclamation-circle"></i> Not set</span>
                    @endif
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0">
                    <span class="text-muted-custom">STK Push</span>
                    @if($chama->mpesa_consumer_key && $chama->mpesa_passkey)
                        <span class="badge-custom badge-success">Enabled</span>
                    @else
                        <span class="badge-custom badge-warning">Manual only</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Settings Form -->
    <div class="col-lg-8">

        <form method="POST" action="{{ route('chama.settings.update') }}">
            @csrf @method('PUT')

            <!-- GENERAL SETTINGS -->
            <div class="card mb-3">
                <div class="card-header-custom">
                    <span class="card-title-custom">General Information</span>
                </div>
                <div class="card-body-custom">

                    <div class="row g-3 mb-3">
                        <div class="col-sm-8">
                            <label class="form-label-custom">Chama Name</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-people input-icon"></i>
                                <input type="text" name="name"
                                       value="{{ old('name', $chama->name) }}"
                                       class="form-control-custom input-with-icon" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label-custom">Category</label>
                            <select name="category" class="form-control-custom">
                                <option value="general"    {{ ($chama->category ?? 'general') === 'general'    ? 'selected' : '' }}>General</option>
                                <option value="women"      {{ ($chama->category) === 'women'      ? 'selected' : '' }}>Women's Group</option>
                                <option value="youth"      {{ ($chama->category) === 'youth'      ? 'selected' : '' }}>Youth Group</option>
                                <option value="investment" {{ ($chama->category) === 'investment' ? 'selected' : '' }}>Investment Club</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Description</label>
                        <textarea name="description" class="form-control-custom" rows="2"
                                  placeholder="Brief description of your chama group...">{{ old('description', $chama->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label-custom">Location</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-geo-alt input-icon"></i>
                                <input type="text" name="location"
                                       value="{{ old('location', $chama->location) }}"
                                       class="form-control-custom input-with-icon"
                                       placeholder="e.g. Nairobi, Kenya">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-custom">Chama Phone</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-phone input-icon"></i>
                                <input type="text" name="phone"
                                       value="{{ old('phone', $chama->phone) }}"
                                       class="form-control-custom input-with-icon"
                                       placeholder="Official chama phone number">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label-custom">Contribution Amount (KES)</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-cash input-icon"></i>
                                <input type="number" name="contribution_amount"
                                       value="{{ old('contribution_amount', $chama->contribution_amount) }}"
                                       class="form-control-custom input-with-icon" min="100" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-custom">Frequency</label>
                            <select name="contribution_frequency" class="form-control-custom">
                                <option value="monthly"   {{ $chama->contribution_frequency === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                                <option value="weekly"    {{ $chama->contribution_frequency === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                                <option value="quarterly" {{ $chama->contribution_frequency === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- M-PESA SETTINGS -->
            <div class="card mb-3">
                <div class="card-header-custom">
                    <span class="card-title-custom">M-Pesa Configuration</span>
                    <span class="badge-custom badge-info">Required for payments</span>
                </div>
                <div class="card-body-custom">

                    <div class="alert-custom alert-info mb-3">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>Basic setup:</strong> Add your Paybill/Till number so members know where to send contributions manually.
                            <br>
                            <strong>Advanced setup:</strong> Add Daraja API keys to enable automatic M-Pesa STK push — members get a payment prompt on their phones.
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label-custom">M-Pesa Type</label>
                            <select name="mpesa_type" class="form-control-custom" id="mpesa_type_select"
                                    onchange="toggleAccountName()">
                                <option value="paybill" {{ ($chama->mpesa_type ?? 'paybill') === 'paybill' ? 'selected' : '' }}>Paybill</option>
                                <option value="till"    {{ ($chama->mpesa_type) === 'till'    ? 'selected' : '' }}>Till Number</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label-custom" id="shortcode-lbl">Paybill Number</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-phone input-icon"></i>
                                <input type="text" name="mpesa_shortcode"
                                       value="{{ old('mpesa_shortcode', $chama->mpesa_shortcode) }}"
                                       class="form-control-custom input-with-icon {{ $errors->has('mpesa_shortcode') ? 'error' : '' }}"
                                       placeholder="e.g. 522533" required>
                            </div>
                            @error('mpesa_shortcode')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4" id="account-name-field">
                            <label class="form-label-custom">Account Name</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-badge input-icon"></i>
                                <input type="text" name="mpesa_account_name"
                                       value="{{ old('mpesa_account_name', $chama->mpesa_account_name) }}"
                                       class="form-control-custom input-with-icon"
                                       placeholder="e.g. Wambua Chama">
                            </div>
                        </div>
                    </div>

                    <!-- Daraja API Keys -->
                    <div class="settings-divider">
                        <span>Daraja API Keys (Optional — for automatic STK push)</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label-custom">Consumer Key</label>
                            <input type="password" name="mpesa_consumer_key"
                                   class="form-control-custom"
                                   placeholder="{{ $chama->mpesa_consumer_key ? '••••••••••••' : 'From Safaricom Developer Portal' }}">
                            <div class="form-hint">Leave blank to keep existing key</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-custom">Consumer Secret</label>
                            <input type="password" name="mpesa_consumer_secret"
                                   class="form-control-custom"
                                   placeholder="{{ $chama->mpesa_consumer_secret ? '••••••••••••' : 'From Safaricom Developer Portal' }}">
                            <div class="form-hint">Leave blank to keep existing secret</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Lipa Na M-Pesa Passkey</label>
                        <input type="password" name="mpesa_passkey"
                               class="form-control-custom"
                               placeholder="{{ $chama->mpesa_passkey ? '••••••••••••' : 'From Safaricom Developer Portal' }}">
                        <div class="form-hint">
                            Get your API keys at
                            <a href="https://developer.safaricom.co.ke" target="_blank" class="text-primary-custom">developer.safaricom.co.ke</a>
                        </div>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn-primary-custom btn-lg">
                <i class="bi bi-check-lg"></i> Save Settings
            </button>

        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.settings-divider {
    display: flex; align-items: center; gap: 12px;
    margin: 20px 0 16px; font-size: 12px; color: var(--text-muted); font-weight: 600;
}
.settings-divider::before, .settings-divider::after {
    content: ''; flex: 1; height: 1px; background: #f1f5f9;
}
</style>
@endpush

@push('scripts')
<script>
function toggleAccountName() {
    const type = document.getElementById('mpesa_type_select').value;
    const accountField = document.getElementById('account-name-field');
    const label = document.getElementById('shortcode-lbl');
    accountField.style.display = type === 'till' ? 'none' : 'block';
    label.textContent = type === 'till' ? 'Till Number' : 'Paybill Number';
}

function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const btn = event.target.closest('button');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        setTimeout(() => btn.innerHTML = original, 2000);
    });
}

toggleAccountName();
</script>
@endpush