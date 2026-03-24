<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'       => ['required', 'string', 'max:20', 'regex:/^(\+?254|0)[17]\d{8}$/'],
            'chama_code'  => ['required', 'string', 'exists:chamas,code'],
            'password'    => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'       => 'Enter a valid Kenyan phone number (e.g. 0712345678).',
            'chama_code.exists' => 'The chama group code you entered does not exist. Contact your administrator.',
            'email.unique'      => 'This email address is already registered.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Passwords do not match.',
        ];
    }
}