<?php

namespace App\Services\Payments;

use App\Models\IntegrationConfig;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayGatewayService
{
    private const API_BASE = 'https://api.razorpay.com/v1';

    public function isConfigured(): bool
    {
        $config = IntegrationConfig::current();

        return (bool) $config->razorpay_enabled
            && $this->keyId() !== ''
            && $this->keySecret() !== '';
    }

    public function keyId(): string
    {
        $config = IntegrationConfig::current();

        return (string) ($config->razorpay_key_id ?: config('himcab.razorpay.key_id', ''));
    }

    public function keySecret(): string
    {
        $config = IntegrationConfig::current();

        return (string) ($config->razorpay_key_secret ?: config('himcab.razorpay.key_secret', ''));
    }

    /**
     * @return array{order_id: string, amount: int, currency: string, receipt: string}
     */
    public function createOrder(float $amountInr, string $receipt, string $notes = 'HimCab driver commission'): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Razorpay is not configured.');
        }

        $amountPaise = (int) round($amountInr * 100);
        if ($amountPaise < 100) {
            throw new RuntimeException('Minimum payment amount is ₹1.');
        }

        $response = Http::withBasicAuth($this->keyId(), $this->keySecret())
            ->acceptJson()
            ->post(self::API_BASE.'/orders', [
                'amount' => $amountPaise,
                'currency' => 'INR',
                'receipt' => $receipt,
                'notes' => [
                    'purpose' => $notes,
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Razorpay order failed: '.$response->body());
        }

        $data = $response->json();

        return [
            'order_id' => (string) ($data['id'] ?? ''),
            'amount' => (int) ($data['amount'] ?? $amountPaise),
            'currency' => (string) ($data['currency'] ?? 'INR'),
            'receipt' => (string) ($data['receipt'] ?? $receipt),
        ];
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $this->keySecret());

        return hash_equals($expected, $signature);
    }
}
