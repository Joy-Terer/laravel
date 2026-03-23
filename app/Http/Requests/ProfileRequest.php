<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+?254|0)[17]\d{8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Kenyan phone number (e.g. 0712345678).',
            'email.unique'=> 'This email address is already used by another account.',
        ];
    }
}