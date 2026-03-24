<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Contribution;

class LoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $maxLoan = Contribution::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->sum('amount') * 2;

        return [
            'amount'           => ['required', 'numeric', 'min:100', 'max:' . max($maxLoan, 100)],
            'repayment_period' => ['required', 'integer', 'min:1', 'max:12'],
            'purpose'          => ['required', 'string', 'min:5', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.max'           => 'Loan amount exceeds your maximum eligibility (2× your total contributions).',
            'amount.min'           => 'Minimum loan amount is KES 100.',
            'purpose.min'          => 'Please describe the purpose of your loan in at least 5 characters.',
            'repayment_period.max' => 'Repayment period cannot exceed 12 months.',
        ];
    }
}