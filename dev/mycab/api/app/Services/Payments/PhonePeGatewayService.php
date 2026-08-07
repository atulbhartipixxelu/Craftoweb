<?php

namespace App\Services\Payments;

use App\Models\IntegrationConfig;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PhonePeGatewayService
{
    public function isConfigured(): bool
    {
        $config = IntegrationConfig::current();

        return (bool) $config->phonepe_enabled
            && $this->merchantId() !== ''
            && $this->saltKey() !== '';
    }

    public function merchantId(): string
    {
        $config = IntegrationConfig::current();

        return (string) ($config->phonepe_merchant_id ?: config('himcab.phonepe.merchant_id', ''));
    }

    public function saltKey(): string
    {
        $config = IntegrationConfig::current();

        return (string) ($config->phonepe_salt_key ?: config('himcab.phonepe.salt_key', ''));
    }

    public function saltIndex(): int
    {
        $config = IntegrationConfig::current();

        return (int) ($config->phonepe_salt_index ?: config('himcab.phonepe.salt_index', 1));
    }

    public function isProduction(): bool
    {
        $config = IntegrationConfig::current();
        $env = (string) ($config->phonepe_env ?: config('himcab.phonepe.env', 'sandbox'));

        return $env === 'production';
    }

    public function apiBase(): string
    {
        return $this->isProduction()
            ? 'https://api.phonepe.com/apis/hermes'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    /**
     * @return array{redirect_url: string, merchant_transaction_id: string}
     */
    public function createPayPage(
        string $merchantTransactionId,
        int $amountPaise,
        string $merchantUserId,
        string $redirectUrl,
        string $callbackUrl,
    ): array {
        if (! $this->isConfigured()) {
            throw new RuntimeException('PhonePe is not configured.');
        }

        if ($amountPaise < 100) {
            throw new RuntimeException('Minimum payment amount is ₹1.');
        }

        $payload = [
            'merchantId' => $this->merchantId(),
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => $merchantUserId,
            'amount' => $amountPaise,
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => $callbackUrl,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $base64 = base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $path = '/pg/v1/pay';
        $checksum = $this->checksum($base64, $path);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
            'X-MERCHANT-ID' => $this->merchantId(),
        ])->post($this->apiBase().$path, [
            'request' => $base64,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('PhonePe payment failed: '.$response->body());
        }

        $body = $response->json();
        $data = $body['data'] ?? [];

        if (($body['success'] ?? false) !== true || empty($data['instrumentResponse']['redirectInfo']['url'])) {
            throw new RuntimeException('PhonePe did not return a redirect URL.');
        }

        return [
            'redirect_url' => (string) $data['instrumentResponse']['redirectInfo']['url'],
            'merchant_transaction_id' => $merchantTransactionId,
        ];
    }

    /**
     * @return array{success: bool, state: string|null, transaction_id: string|null}
     */
    public function checkStatus(string $merchantTransactionId): array
    {
        $path = '/pg/v1/status/'.$this->merchantId().'/'.$merchantTransactionId;
        $checksum = $this->statusChecksum($path);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
            'X-MERCHANT-ID' => $this->merchantId(),
        ])->get($this->apiBase().$path);

        if (! $response->successful()) {
            return ['success' => false, 'state' => null, 'transaction_id' => null];
        }

        $body = $response->json();
        $data = $body['data'] ?? [];

        return [
            'success' => ($body['success'] ?? false) === true,
            'state' => $data['state'] ?? null,
            'transaction_id' => $data['transactionId'] ?? null,
        ];
    }

    public function verifyCallbackChecksum(string $base64Response, string $providedChecksum): bool
    {
        $expected = $this->checksum($base64Response, '/pg/v1/status/'.$this->merchantId());

        return hash_equals($expected, $providedChecksum);
    }

    private function checksum(string $base64Payload, string $path): string
    {
        $hash = hash('sha256', $base64Payload.$path.$this->saltKey());

        return $hash.'###'.$this->saltIndex();
    }

    private function statusChecksum(string $path): string
    {
        $hash = hash('sha256', $path.$this->saltKey());

        return $hash.'###'.$this->saltIndex();
    }
}
