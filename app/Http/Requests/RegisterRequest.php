<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:50','regex:/^[a-zA-Z]+$/'],
            'last_name'   => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z]+$/'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'       => ['required', 'string', 'max:20', 'regex:/^(\+?254|0)[17]\d{8}$/'],
            'chama_code'  => ['required', 'string', 'exists:chamas,code'],
            'password'    => ['required', 'confirmed', 
                Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()  //checks against known data breaches

                ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'       => 'Enter a valid Kenyan phone number (e.g. 0712345678).',
            'chama_code.exists' => 'Invalid chama code! Please check and try again.',
            'email.unique'      => 'This email address is already registered.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Invalid input! Please try again.',
        ];
    }
}