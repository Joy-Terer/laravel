@extends('layouts.app')
@section('title','New Contribution')
@section('page-title','New Contribution')
@section('page-subtitle','Choose your payment method and amount')

@section('content')

<div class="row justify-content-center">
<div class="col-lg-7">

    <!-- PAYMENT METHOD SELECTOR -->
    <div class="card mb-3">
        <div class="card-header-custom">
            <span class="card-title-custom">Select Payment Method</span>
        </div>
        <div class="card-body-custom">
            <div class="payment-method-grid" id="methodSelector">
                <div class="method-card" data-method="mpesa" onclick="selectMethod('mpesa')">
                    <i class="bi bi-phone method-icon text-primary-custom"></i>
                    <span class="method-label">M-Pesa</span>
                </div>
                <div class="method-card" data-method="paypal" onclick="selectMethod('paypal')">
                    <i class="bi bi-globe method-icon" style="color:#0070ba"></i>
                    <span class="method-label">PayPal</span>
                </div>
                <div class="method-card" data-method="wave" onclick="selectMethod('wave')">
                    <i class="bi bi-send method-icon text-info-custom"></i>
                    <span class="method-label">Wave</span>
                </div>
                <div class="method-card" data-method="cash" onclick="selectMethod('cash')">
                    <i class="bi bi-cash-stack method-icon text-success-custom"></i>
                    <span class="method-label">Cash</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTRIBUTION FORM -->
    <div class="card">
        <div class="card-header-custom">
            <span class="card-title-custom">Contribution Details</span>
            <span id="methodBadge" class="badge-custom badge-primary" style="display:none"></span>
        </div>
        <div class="card-body-custom">
            <form method="POST" action="{{ route('contributions.store') }}">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method" value="mpesa">

                <div class="form-group mb-4">
                    <label class="form-label-custom">Contribution Amount (KES)</label>
                    <div class="amount-input-wrap">
                        <span class="amount-prefix">KES</span>
                        <input type="number" name="amount" id="amount"
                               value="{{ old('amount', $chama->contribution_amount ?? 2000) }}"
                               class="form-control-custom amount-input"
                               min="1" step="1" required>
                    </div>
                    <div class="form-hint">
                        Standard contribution: <strong>KES {{ number_format($chama->contribution_amount ?? 2000, 0) }}</strong>
                    </div>
                    @error('amount')
                        <div class="form-error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- M-PESA SECTION -->
                <div id="mpesa-section" class="payment-section">
                    <div class="alert-custom alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>How M-Pesa works:</strong>
                            Click Pay Now and you'll receive a payment prompt on your phone. Enter your M-Pesa PIN to confirm.
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label-custom">M-Pesa Phone Number</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-phone input-icon"></i>
                            <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                                   class="form-control-custom input-with-icon"
                                   placeholder="07XXXXXXXX">
                        </div>
                    </div>
                </div>

                <!-- PAYPAL SECTION -->
                <div id="paypal-section" class="payment-section" style="display:none">
                    <div class="alert-custom alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>PayPal Payment:</strong>
                            You'll be redirected to PayPal. The amount will be converted to USD at the current exchange rate.
                        </div>
                    </div>
                </div>

                <!-- WAVE SECTION -->
                <div id="wave-section" class="payment-section" style="display:none">
                    <div class="alert-custom alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>Wave Payment:</strong>
                            You'll be redirected to Wave. Supported for East Africa diaspora members.
                        </div>
                    </div>
                </div>

                <!-- CASH SECTION -->
                <div id="cash-section" class="payment-section" style="display:none">
                    <div class="alert-custom alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <div>
                            <strong>Cash Payment:</strong>
                            Only the Treasurer can record cash payments. Members must pay in person and the Treasurer will verify.
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label-custom">Notes (Optional)</label>
                    <textarea name="notes" class="form-control-custom" rows="2"
                              placeholder="e.g. March 2025 monthly contribution">{{ old('notes') }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary-custom btn-lg" id="submitBtn">
                        <i class="bi bi-check-circle"></i>
                        <span id="submitText">Pay with M-Pesa</span>
                    </button>
                    <a href="{{ route('contributions.index') }}" class="btn-outline-custom btn-lg">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
const methodConfig = {
    mpesa:  { label: 'M-Pesa',  btn: 'Pay with M-Pesa',  cls: 'selected-mpesa'  },
    paypal: { label: 'PayPal',  btn: 'Pay with PayPal',  cls: 'selected-paypal' },
    wave:   { label: 'Wave',    btn: 'Pay with Wave',    cls: 'selected-wave'   },
    cash:   { label: 'Cash',    btn: 'Record Cash',      cls: 'selected-cash'   },
};

function selectMethod(m) {
    document.getElementById('payment_method').value = m;

    document.querySelectorAll('.method-card').forEach(c => {
        c.className = 'method-card';
    });
    document.querySelector(`[data-method="${m}"]`).classList.add(methodConfig[m].cls);

    document.querySelectorAll('.payment-section').forEach(s => s.style.display = 'none');
    document.getElementById(`${m}-section`).style.display = 'block';

    const badge = document.getElementById('methodBadge');
    badge.textContent = methodConfig[m].label;
    badge.style.display = 'inline-flex';

    document.getElementById('submitText').textContent = methodConfig[m].btn;
}

selectMethod('mpesa');
</script>
@endpush