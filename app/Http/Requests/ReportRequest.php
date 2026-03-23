<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:monthly,annual,custom'],
            'from' => ['required', 'date', 'before_or_equal:to'],
            'to'   => ['required', 'date', 'after_or_equal:from'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.before_or_equal' => 'The start date must be before or equal to the end date.',
            'to.after_or_equal'    => 'The end date must be after or equal to the start date.',
        ];
    }
}