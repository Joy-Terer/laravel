<?php

namespace App\Services;

use App\Models\Chama;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class MpesaService
{
    private string $baseUrl;

    public function __construct()
    {
        $env = config('mpesa.env', 'sandbox');
        $this->baseUrl = $env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    // ── Get access token (per-chama or platform) ──────────────────
    private function getAccessToken(
        string $consumerKey,
        string $consumerSecret
    ): ?string {
        try {
            $credentials = base64_encode("{$consumerKey}:{$consumerSecret}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
            ])->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('M-Pesa token error: ' . $e->getMessage());
            return null;
        }
    }

    // ── STK Push (uses chama's own credentials if available) ──────
    public function stkPush(
        string  $phone,
        int     $amount,
        string  $ref,
        string  $desc,
        ?Chama  $chama = null
    ): array {
        // Use chama's credentials if they have their own, else fall back to platform
        $consumerKey    = ($chama?->mpesa_consumer_key) 
        ?Crypt::decrypt($chama->mpesa_consumer_key): config('mpesa.consumer_key');
        $consumerSecret = ($chama?->mpesa_consumer_secret) 
        ?Crypt::decrypt($chama->mpesa_consumer_secret): config('mpesa.consumer_secret');
        $shortCode      = ($chama?->mpesa_shortcode) ?: config('mpesa.shortcode');
        $passKey        = ($chama?->mpesa_passkey) 
        ?Crypt::decrypt($chama->mpesa_passkey): config('mpesa.passkey');
        $callbackUrl    = config('mpesa.callback_url');

        if (!$consumerKey || !$consumerSecret) {
            return [
                'success' => false,
                'message' => 'M-Pesa is not configured for this chama. Please update your chama settings.',
            ];
        }

        $token = $this->getAccessToken($consumerKey, $consumerSecret);

        if (!$token) {
            return ['success' => false, 'message' => 'Could not get M-Pesa access token.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($shortCode . $passKey . $timestamp);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $shortCode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => $chama?->mpesa_type === 'till'
                        ? 'CustomerBuyGoodsOnline'
                        : 'CustomerPayBillOnline',
                    'Amount'            => $amount,
                    'PartyA'            => $phone,
                    'PartyB'            => $shortCode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $callbackUrl,
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