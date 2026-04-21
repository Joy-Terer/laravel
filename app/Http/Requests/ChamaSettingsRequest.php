<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChamaSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $chamaId = auth()->user()->chama_id;

        return [
            'name'                   => ['required', 'string', 'max:100'],
            'description'            => ['nullable', 'string', 'max:500'],
            'category'               => ['required', 'in:general,women,youth,investment'],
            'location'               => ['nullable', 'string', 'max:100'],
            'meeting_day'            => ['nullable', 'string', 'max:50'],
            'contribution_amount'    => ['required', 'numeric', 'min:100'],
            'contribution_frequency' => ['required', 'in:weekly,monthly,quarterly'],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'mpesa_type'             => ['required', 'in:paybill,till,pochi la biashara,send money'],
            'mpesa_shortcode'        => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'mpesa_account_name'     => ['nullable', 'string', 'max:100'],
            'mpesa_consumer_key'     => ['nullable', 'string'],
            'mpesa_consumer_secret'  => ['nullable', 'string'],
            'mpesa_passkey'          => ['nullable', 'string'],
            'logo'=>['nullable', 'image','mimes:png,jpg,webp', 'max:2048']
        ];
    }

    public function messages(): array
    {
        return [
            'mpesa_shortcode.regex' => 'M-Pesa shortcode must be numbers only.',
        ];
    }
}