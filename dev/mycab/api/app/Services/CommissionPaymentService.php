<?php

namespace App\Services;

use App\Models\CommissionPayment;
use App\Models\Driver;
use App\Models\DriverCommission;
use App\Models\IntegrationConfig;
use App\Services\Payments\PhonePeGatewayService;
use App\Services\Payments\RazorpayGatewayService;
use Illuminate\Support\Str;
use RuntimeException;

class CommissionPaymentService
{
    public function __construct(
        private readonly DriverCommissionService $commissions,
        private readonly RazorpayGatewayService $razorpay,
        private readonly PhonePeGatewayService $phonepe,
    ) {}

    /**
     * @return list<array{id: string, label: string}>
     */
    public function availableGateways(): array
    {
        $gateways = [];

        if ($this->razorpay->isConfigured()) {
            $gateways[] = ['id' => CommissionPayment::GATEWAY_RAZORPAY, 'label' => 'Razorpay'];
        }

        if ($this->phonepe->isConfigured()) {
            $gateways[] = ['id' => CommissionPayment::GATEWAY_PHONEPE, 'label' => 'PhonePe'];
        }

        return $gateways;
    }

    public function resolveCommissionForPayment(Driver $driver, ?int $year = null, ?int $month = null): DriverCommission
    {
        if ($year === null || $month === null) {
            [$year, $month] = $this->commissions->previousMonthPeriod();
        }

        $record = $this->commissions->ensureCommissionRecord($driver, $year, $month);

        if ($record->status === DriverCommission::STATUS_PAID) {
            throw new RuntimeException('Commission for this period is already paid.');
        }

        if ($record->commission_amount < 1) {
            throw new RuntimeException('No commission is due for this period.');
        }

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    public function initiate(Driver $driver, string $gateway, ?int $year = null, ?int $month = null): array
    {
        $commission = $this->resolveCommissionForPayment($driver, $year, $month);
        $merchantTxnId = $this->merchantTransactionId($commission, $gateway);

        $payment = CommissionPayment::query()->updateOrCreate(
            ['merchant_transaction_id' => $merchantTxnId],
            [
                'driver_commission_id' => $commission->id,
                'driver_id' => $driver->id,
                'gateway' => $gateway,
                'amount' => $commission->commission_amount,
                'currency' => 'INR',
                'status' => CommissionPayment::STATUS_CREATED,
            ],
        );

        if ($payment->status === CommissionPayment::STATUS_PAID) {
            throw new RuntimeException('This payment was already completed.');
        }

        return match ($gateway) {
            CommissionPayment::GATEWAY_RAZORPAY => $this->initiateRazorpay($payment, $commission, $driver),
            CommissionPayment::GATEWAY_PHONEPE => $this->initiatePhonePe($payment, $commission, $driver),
            default => throw new RuntimeException('Unsupported payment gateway.'),
        };
    }

    public function verifyRazorpay(
        Driver $driver,
        string $orderId,
        string $paymentId,
        string $signature,
        ?int $year = null,
        ?int $month = null,
    ): CommissionPayment {
        if (! $this->razorpay->verifyPaymentSignature($orderId, $paymentId, $signature)) {
            throw new RuntimeException('Razorpay payment verification failed.');
        }

        $payment = CommissionPayment::query()
            ->where('driver_id', $driver->id)
            ->where('gateway', CommissionPayment::GATEWAY_RAZORPAY)
            ->where('gateway_order_id', $orderId)
            ->firstOrFail();

        return $this->completePayment($payment, $paymentId, [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
        ]);
    }

    public function completeByMerchantTransactionId(string $merchantTransactionId): ?CommissionPayment
    {
        $payment = CommissionPayment::query()
            ->where('merchant_transaction_id', $merchantTransactionId)
            ->first();

        if ($payment === null || $payment->status === CommissionPayment::STATUS_PAID) {
            return $payment;
        }

        if ($payment->gateway === CommissionPayment::GATEWAY_PHONEPE) {
            $status = $this->phonepe->checkStatus($merchantTransactionId);
            if (($status['state'] ?? '') !== 'COMPLETED') {
                $payment->update(['status' => CommissionPayment::STATUS_FAILED]);

                return $payment->fresh();
            }

            return $this->completePayment($payment, (string) ($status['transaction_id'] ?? $merchantTransactionId), $status);
        }

        return $payment;
    }

    /**
     * @return array<string, mixed>
     */
    public function paymentMetaForDriver(Driver $driver): array
    {
        [$year, $month] = $this->commissions->previousMonthPeriod();
        $record = $this->commissions->ensureCommissionRecord($driver, $year, $month);
        $gateways = $this->availableGateways();

        return [
            'gateways' => $gateways,
            'can_pay_online' => $gateways !== [] && $record->status !== DriverCommission::STATUS_PAID && $record->commission_amount >= 1,
            'payable' => [
                'year' => $record->period_year,
                'month' => $record->period_month,
                'label' => $record->periodLabel(),
                'amount' => $record->commission_amount,
                'status' => $record->status,
            ],
        ];
    }

    private function completePayment(CommissionPayment $payment, string $gatewayPaymentId, array $payload): CommissionPayment
    {
        $payment->update([
            'status' => CommissionPayment::STATUS_PAID,
            'gateway_payment_id' => $gatewayPaymentId,
            'gateway_payload' => $payload,
            'paid_at' => now(),
        ]);

        $commission = $payment->driverCommission()->firstOrFail();
        $this->commissions->markPaidViaGateway(
            $commission,
            $payment->gateway,
            $gatewayPaymentId,
        );

        return $payment->fresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function initiateRazorpay(CommissionPayment $payment, DriverCommission $commission, Driver $driver): array
    {
        $order = $this->razorpay->createOrder(
            (float) $commission->commission_amount,
            $payment->merchant_transaction_id,
            'HimCab commission '.$commission->periodLabel(),
        );

        $payment->update([
            'status' => CommissionPayment::STATUS_PENDING,
            'gateway_order_id' => $order['order_id'],
            'gateway_payload' => $order,
        ]);

        return [
            'gateway' => CommissionPayment::GATEWAY_RAZORPAY,
            'merchant_transaction_id' => $payment->merchant_transaction_id,
            'razorpay' => [
                'key_id' => $this->razorpay->keyId(),
                'order_id' => $order['order_id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'name' => config('app.name', 'HimCab'),
                'description' => 'Platform commission — '.$commission->periodLabel(),
                'prefill' => [
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'contact' => $driver->phone,
                ],
            ],
            'period' => [
                'year' => $commission->period_year,
                'month' => $commission->period_month,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function initiatePhonePe(CommissionPayment $payment, DriverCommission $commission, Driver $driver): array
    {
        $frontend = rtrim((string) (IntegrationConfig::current()->frontend_url ?: config('app.frontend_url', config('himcab.frontend_url', ''))), '/');
        $apiBase = rtrim((string) config('app.url'), '/');

        $redirectUrl = $frontend.'/driver/profile?commission_payment=phonepe&mtId='.$payment->merchant_transaction_id;
        $callbackUrl = $apiBase.'/payments/phonepe/callback';

        $result = $this->phonepe->createPayPage(
            $payment->merchant_transaction_id,
            (int) round($commission->commission_amount * 100),
            'driver_'.$driver->id,
            $redirectUrl,
            $callbackUrl,
        );

        $payment->update([
            'status' => CommissionPayment::STATUS_PENDING,
            'gateway_payload' => $result,
        ]);

        return [
            'gateway' => CommissionPayment::GATEWAY_PHONEPE,
            'merchant_transaction_id' => $payment->merchant_transaction_id,
            'phonepe' => [
                'redirect_url' => $result['redirect_url'],
            ],
            'period' => [
                'year' => $commission->period_year,
                'month' => $commission->period_month,
            ],
        ];
    }

    private function merchantTransactionId(DriverCommission $commission, string $gateway): string
    {
        $prefix = strtoupper(substr($gateway, 0, 2));
        $suffix = Str::upper(Str::random(8));

        return sprintf('HC%s%d%02d%d%s', $prefix, $commission->period_year, $commission->period_month, $commission->driver_id, $suffix);
    }
}
