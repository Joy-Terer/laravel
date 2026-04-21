@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account details')

@section('content')

<div class="row g-3">

    <!-- LEFT: Avatar & Summary -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body-custom text-center" style="padding:32px 20px">
                <div class="profile-avatar-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>
                <span class="badge-custom {{ auth()->user()->status === 'active' ? 'badge-success' : 'badge-warning' }}" style="margin-top:8px">
                    {{ ucfirst(auth()->user()->status) }}
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-header-custom">
                <span class="card-title-custom">Account Summary</span>
            </div>
            <div class="card-body-custom">
                <div class="profile-summary-row">
                    <span class="profile-summary-label">Chama Group</span>
                    <span class="profile-summary-value">{{ auth()->user()->chama->name ?? '—' }}</span>
                </div>
                <div class="profile-summary-row">
                    <span class="profile-summary-label">Member Since</span>
                    <span class="profile-summary-value">{{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
                <div class="profile-summary-row">
                    <span class="profile-summary-label">Total Contributed</span>
                    <span class="profile-summary-value font-bold text-success-custom">
                        KES {{ number_format(auth()->user()->contributions()->where('status','completed')->sum('amount'), 0) }}
                    </span>
                </div>
                <div class="profile-summary-row">
                    <span class="profile-summary-label">Active Loans</span>
                    <span class="profile-summary-value">
                        {{ auth()->user()->loans()->whereIn('status',['approved','active'])->count() }}
                    </span>
                </div>
                <div class="profile-summary-row" style="border:none">
                    <span class="profile-summary-label">Email Verification</span>
                    <span class="profile-summary-value">
                        @if(auth()->user()->email_verified_at)
                            <span class="badge-custom badge-success"><i class="bi bi-check-circle"></i> Yes</span>
                        @else
                            <span class="badge-custom badge-warning"><i class="bi bi-clock"></i> Pending</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Edit Forms -->
    <div class="col-lg-8">

        <!-- UPDATE PROFILE INFO -->
        <div class="card mb-3">
            <div class="card-header-custom">
                <span class="card-title-custom">Personal Information</span>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label-custom">Full Name</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                       class="form-control-custom input-with-icon {{ $errors->get('name') ? 'error' : '' }}"
                                       required>
                            </div>
                            @error('name')
                                <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-custom">Phone Number (M-Pesa)</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-phone input-icon"></i>
                                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                       class="form-control-custom input-with-icon {{ $errors->get('phone') ? 'error' : '' }}"
                                       placeholder="07XXXXXXXX">
                            </div>
                            @error('phone')
                                <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                   class="form-control-custom input-with-icon {{ $errors->get('email') ? 'error' : '' }}"
                                   required>
                        </div>
                        @error('email')
                            <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- UPDATE PASSWORD -->
        <div class="card mb-3">
            <div class="card-header-custom">
                <span class="card-title-custom">Change Password</span>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label-custom">Current Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="current_password"
                                   class="form-control-custom input-with-icon {{ $errors->updatePassword->get('current_password') ? 'error' : '' }}"
                                   placeholder="Enter current password">
                        </div>
                        @error('current_password', 'updatePassword')
                            <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label-custom">New Password</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" name="password"
                                       class="form-control-custom input-with-icon {{ $errors->updatePassword->get('password') ? 'error' : '' }}"
                                       placeholder="Min. 8 characters">
                            </div>
                            @error('password', 'updatePassword')
                                <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-custom">Confirm New Password</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" name="password_confirmation"
                                       class="form-control-custom input-with-icon"
                                       placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-shield-check"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
        <!-- LOGOUT -->
        <div class="card mb-3">
            <div class="card-body-custom">
                <span class="card-title-custom">Logout from all devices</span>
                <p style="font-size:13px;color:var(--text-secondary);margin:8px 0 16px">
                    This will log you out from all active sessions on other devices. You will need to log in again on those devices to regain access.
                </p>
                <form method="POST" action="{{ route('profile.logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline-custom">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- DELETE ACCOUNT -->
        <div class="card card-danger-border">
            <div class="card-header-custom">
                <span class="card-title-custom text-danger-custom">Delete Account</span>
            </div>
            <div class="card-body-custom">
                <p style="font-size:13px;color:var(--text-secondary);margin-bottom:16px">
                    WARNING! Once your account is deleted, all data will be permanently removed. Please make sure to download any important information before proceeding.
                </p>
                <button type="button" class="btn-danger-custom btn-sm"
                        onclick="document.getElementById('deleteModal').style.display='flex'">
                    <i class="bi bi-trash"></i> Delete My Account
                </button>
            </div>
        </div>

    </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-icon-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h3 class="modal-title">Delete Account?</h3>
        <p class="modal-desc">This will permanently delete your account and all associated data. This cannot be undone.</p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')
            <div class="mb-4">
                <label class="form-label-custom">Enter your password to confirm</label>
                <input type="password" name="password" class="form-control-custom" placeholder="Your password" required>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-danger-custom" style="flex:1;justify-content:center">
                    <i class="bi bi-trash"></i> Yes, Delete
                </button>
                <button type="button" class="btn-outline-custom" style="flex:1;justify-content:center"
                        onclick="document.getElementById('deleteModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.profile-avatar-lg {
    width: 72px; height: 72px; border-radius: 50%;
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 800; color: white;
    margin: 0 auto 14px;
}
.profile-name { font-size: 17px; font-weight: 700; color: var(--text-primary); }
.profile-role { font-size: 12px; color: var(--text-muted); text-transform: capitalize; margin-top: 3px; }
.profile-summary-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;
}
.profile-summary-label { color: var(--text-muted); font-size: 12px; }
.profile-summary-value { font-weight: 600; color: var(--text-primary); }
.card-danger-border { border-color: #fecaca; }
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.5);
    display: flex; align-items: center; justify-content: center; z-index: 9999;
}
.modal-box {
    background: white; border-radius: var(--radius-lg);
    padding: 32px; max-width: 420px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.modal-icon-danger {
    width: 52px; height: 52px; border-radius: 50%; background: var(--danger-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: var(--danger); margin: 0 auto 16px;
}
.modal-title { font-size: 18px; font-weight: 700; text-align: center; margin-bottom: 8px; }
.modal-desc  { font-size: 13px; color: var(--text-secondary); text-align: center; margin-bottom: 20px; }
.modal-actions { display: flex; gap: 10px; }
</style>
@endpush
