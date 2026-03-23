@extends('layouts.app')
@section('title','Apply for Loan')
@section('page-title','Loan Application')
@section('page-subtitle','Apply for a loan from your chama')

@section('content')

<div class="row justify-content-center">
<div class="col-lg-7">

    <div class="alert-custom alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            You are eligible to borrow up to
            <strong>KES {{ number_format($maxLoan ?? 0, 0) }}</strong>
            (2× your total contributions of KES {{ number_format($totalContributions ?? 0, 0) }})
        </div>
    </div>

    <div class="card">
        <div class="card-header-custom">
            <span class="card-title-custom">Loan Application Form</span>
        </div>
        <div class="card-body-custom">
            <form method="POST" action="{{ route('loans.store') }}">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label-custom">Loan Amount (KES) *</label>
                        <div class="amount-input-wrap">
                            <span class="amount-prefix">KES</span>
                            <input type="number" name="amount" value="{{ old('amount') }}"
                                   class="form-control-custom amount-input {{ $errors->has('amount') ? 'error' : '' }}"
                                   placeholder="0" min="100" max="{{ $maxLoan ?? 100000 }}" required>
                        </div>
                        @error('amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label-custom">Repayment Period *</label>
                        <select name="repayment_period" class="form-control-custom {{ $errors->has('repayment_period') ? 'error' : '' }}" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('repayment_period') == $i ? 'selected' : '' }}>
                                    {{ $i }} month{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                        @error('repayment_period')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label-custom">Purpose of Loan *</label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}"
                           class="form-control-custom {{ $errors->has('purpose') ? 'error' : '' }}"
                           placeholder="e.g. School fees, Business capital, Medical emergency" required>
                    @error('purpose')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="form-label-custom">Additional Notes (Optional)</label>
                    <textarea name="notes" class="form-control-custom" rows="3"
                              placeholder="Any additional information for the administrator...">{{ old('notes') }}</textarea>
                </div>

                <!-- LOAN SUMMARY PREVIEW -->
                <div class="loan-summary-preview">
                    <div class="loan-summary-title">Loan Summary</div>
                    <div class="loan-summary-grid">
                        <div>
                            <div class="loan-summary-label">Monthly Repayment</div>
                            <div class="loan-summary-value" id="monthlyAmt">KES —</div>
                        </div>
                        <div>
                            <div class="loan-summary-label">Total Repayable</div>
                            <div class="loan-summary-value" id="totalAmt">KES —</div>
                        </div>
                    </div>
                </div>

                <div class="terms-row">
                    <input type="checkbox" name="agree" id="agree" required>
                    <label for="agree" class="terms-label">
                        I agree that this loan will be repaid according to the chama's loan policy and the repayment schedule above.
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary-custom btn-lg">
                        <i class="bi bi-send"></i> Submit Application
                    </button>
                    <a href="{{ route('loans.index') }}" class="btn-outline-custom btn-lg">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
function updateSummary() {
    const amount = parseFloat(document.querySelector('[name=amount]').value) || 0;
    const months = parseInt(document.querySelector('[name=repayment_period]').value) || 1;
    const fmt = n => 'KES ' + n.toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    document.getElementById('monthlyAmt').textContent = fmt(amount / months);
    document.getElementById('totalAmt').textContent   = fmt(amount);
}
document.querySelector('[name=amount]').addEventListener('input', updateSummary);
document.querySelector('[name=repayment_period]').addEventListener('change', updateSummary);
</script>
@endpush