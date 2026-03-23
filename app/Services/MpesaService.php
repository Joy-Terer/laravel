<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $baseUrl;
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortCode;
    private string $passKey;
    private string $callbackUrl;

    public function __construct()
    {
        $env = config('mpesa.env', 'sandbox');

        $this->baseUrl        = $env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';

        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortCode      = config('mpesa.shortcode');
        $this->passKey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
    }

    // ── Get OAuth token ───────────────────────────────────────────
    private function getAccessToken(): ?string
    {
        try {
            $credentials = base64_encode("{$this->consumerKey}:{$this->consumerSecret}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
            ])->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('M-Pesa token error: ' . $e->getMessage());
            return null;
        }
    }

    // ── STK Push ──────────────────────────────────────────────────
    public function stkPush(
        string $phone,
        int    $amount,
        string $ref,
        string $desc
    ): array {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Could not get M-Pesa access token.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortCode . $this->passKey . $timestamp);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $this->shortCode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => $amount,
                    'PartyA'            => $phone,
                    'PartyB'            => $this->shortCode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $this->callbackUrl,
                    'AccountReference'  => $ref,
                    'TransactionDesc'   => $desc,
                ]);

            $data = $response->json();

            if (($data['ResponseCode'] ?? '1') === '0') {
                return [
                    'success'             => true,
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'merchant_request_id' => $data['MerchantRequestID'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['ResponseDescription'] ?? 'STK Push failed.',
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK push error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'M-Pesa request failed. Please try again.'];
        }
    }
}