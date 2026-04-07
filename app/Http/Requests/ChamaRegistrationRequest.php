<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChamaRegistrationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Chama details
            'chama_name'          => ['required', 'string', 'max:100'],
            'chama_description'   => ['nullable', 'string', 'max:500'],
            'chama_category'      => ['required', 'in:general,women,youth,investment'],
            'chama_location'      => ['nullable', 'string', 'max:100'],
            'meeting_day'         => ['nullable', 'string', 'max:50'],
            'contribution_amount' => ['required', 'numeric', 'min:100'],
            'contribution_frequency' => ['required', 'in:weekly,monthly,quarterly'],

            // M-Pesa details
            'mpesa_type'          => ['required', 'in:paybill,till'],
            'mpesa_shortcode'     => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'mpesa_account_name'  => ['nullable', 'string', 'max:100'],
            'mpesa_consumer_key'  => ['nullable', 'string'],
            'mpesa_consumer_secret' => ['nullable', 'string'],
            'mpesa_passkey'       => ['nullable', 'string'],

            // Admin details
            'admin_name'          => ['required', 'string', 'max:100'],
            'admin_email'         => ['required', 'email', 'unique:users,email'],
            'admin_phone'         => ['required', 'string', 'regex:/^(\+?254|0)[17]\d{8}$/'],
            'admin_password'      => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'mpesa_shortcode.regex'  => 'M-Pesa shortcode must be numbers only.',
            'admin_phone.regex'      => 'Enter a valid Kenyan phone number.',
            'admin_email.unique'     => 'This email is already registered.',
            'contribution_amount.min'=> 'Minimum contribution amount is KES 100.',
        ];
    }
}


