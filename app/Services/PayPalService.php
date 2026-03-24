<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $baseUrl;
    private string $clientId;
    private string $secret;

    public function __construct()
    {
        $mode = config('paypal.mode', 'sandbox');

        $this->baseUrl  = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->clientId = config('paypal.client_id');
        $this->secret   = config('paypal.client_secret');
    }

    // ── Get OAuth token ───────────────────────────────────────────
    private function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->secret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('PayPal token error: ' . $e->getMessage());
            return null;
        }
    }

    // ── Create order ──────────────────────────────────────────────
    public function createOrder(float $amountKes, int $userId, int $chamaId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Could not get PayPal access token.'];
        }

        // Convert KES to USD (use live exchange rate in production)
        $exchangeRate = config('paypal.exchange_rate', 130);
        $amountUsd    = round($amountKes / $exchangeRate, 2);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent'           => 'CAPTURE',
                    'purchase_units'   => [[
                        'amount'      => [
                            'currency_code' => 'USD',
                            'value'         => (string) $amountUsd,
                        ],
                        'description' => 'Smart Chama Contribution',
                        'custom_id'   => "user_{$userId}_chama_{$chamaId}",
                    ]],
                    'application_context' => [
                        'return_url' => route('paypal.success'),
                        'cancel_url' => route('paypal.cancel'),
                    ],
                ]);

            $data = $response->json();

            if (isset($data['id'])) {
                $approvalUrl = collect($data['links'])
                    ->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success'      => true,
                    'order_id'     => $data['id'],
                    'approval_url' => $approvalUrl,
                ];
            }

            return ['success' => false, 'message' => 'Could not create PayPal order.'];
        } catch (\Exception $e) {
            Log::error('PayPal create order error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'PayPal request failed.'];
        }
    }

    // ── Capture order ─────────────────────────────────────────────
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false];

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            $data = $response->json();

            if (($data['status'] ?? '') === 'COMPLETED') {
                $captureId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
                return ['success' => true, 'transaction_id' => $captureId];
            }

            return ['success' => false, 'message' => 'Payment not completed.'];
        } catch (\Exception $e) {
            Log::error('PayPal capture error: ' . $e->getMessage());
            return ['success' => false];
        }
    }
}