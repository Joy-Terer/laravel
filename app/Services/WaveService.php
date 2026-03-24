<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaveService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $mode = config('wave.mode', 'sandbox');

        $this->baseUrl = $mode === 'live'
            ? 'https://api.wave.com/v1'
            : 'https://api.sandbox.wave.com/v1';

        $this->apiKey = config('wave.api_key');
    }

    // ── Create payment ────────────────────────────────────────────
    public function createPayment(
        float  $amount,
        string $currency = 'KES',
        string $description = 'Smart Chama Contribution',
        string $successUrl = '',
        string $errorUrl = ''
    ): array {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/json',
            ])->post("{$this->baseUrl}/checkout/sessions", [
                'amount'      => (int) ($amount * 100), // Wave uses cents
                'currency'    => $currency,
                'description' => $description,
                'success_url' => $successUrl ?: route('contributions.index'),
                'error_url'   => $errorUrl   ?: route('contributions.create'),
            ]);

            $data = $response->json();

            if (isset($data['id'])) {
                return [
                    'success'      => true,
                    'session_id'   => $data['id'],
                    'checkout_url' => $data['wave_launch_url'] ?? null,
                ];
            }

            return ['success' => false, 'message' => 'Wave payment session creation failed.'];
        } catch (\Exception $e) {
            Log::error('Wave payment error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Wave request failed. Please try again.'];
        }
    }

    // ── Check payment status ──────────────────────────────────────
    public function checkPayment(string $sessionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get("{$this->baseUrl}/checkout/sessions/{$sessionId}");

            $data = $response->json();

            return [
                'success' => ($data['payment_status'] ?? '') === 'succeeded',
                'status'  => $data['payment_status'] ?? 'unknown',
                'ref'     => $data['transaction_id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Wave check error: ' . $e->getMessage());
            return ['success' => false, 'status' => 'error'];
        }
    }
}