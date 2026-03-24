<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContributionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'         => ['required', 'numeric', 'min:1', 'max:1000000'],
            'payment_method' => ['required', 'in:mpesa,paypal,wave,cash'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'        => 'Contribution amount must be at least KES 200.',
            'amount.max'        => 'Contribution amount cannot exceed KES 1,000,000.',
            'payment_method.in' => 'Please select a valid payment method.',
        ];
    }
}