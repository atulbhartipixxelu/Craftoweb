<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionPayment;
use App\Models\Driver;
use App\Services\CommissionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class DriverCommissionPaymentController extends Controller
{
    public function options(Request $request, CommissionPaymentService $payments): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        return response()->json($payments->paymentMetaForDriver($driver));
    }

    public function initiate(Request $request, CommissionPaymentService $payments): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        $validated = $request->validate([
            'gateway' => ['required', 'string', 'in:razorpay,phonepe'],
            'year' => ['sometimes', 'integer', 'min:2020', 'max:2100'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ]);

        try {
            $payload = $payments->initiate(
                $driver,
                $validated['gateway'],
                $validated['year'] ?? null,
                $validated['month'] ?? null,
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($payload);
    }

    public function verifyRazorpay(Request $request, CommissionPaymentService $payments): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
            'year' => ['sometimes', 'integer', 'min:2020', 'max:2100'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ]);

        try {
            $payment = $payments->verifyRazorpay(
                $driver,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature'],
                $validated['year'] ?? null,
                $validated['month'] ?? null,
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Commission payment successful.',
            'payment' => $payment,
        ]);
    }

    public function status(Request $request, CommissionPaymentService $payments): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        $validated = $request->validate([
            'merchant_transaction_id' => ['required', 'string'],
        ]);

        $payment = CommissionPayment::query()
            ->where('driver_id', $driver->id)
            ->where('merchant_transaction_id', $validated['merchant_transaction_id'])
            ->firstOrFail();

        if ($payment->status !== CommissionPayment::STATUS_PAID) {
            $payment = $payments->completeByMerchantTransactionId($payment->merchant_transaction_id) ?? $payment;
        }

        return response()->json([
            'status' => $payment->status,
            'paid' => $payment->status === CommissionPayment::STATUS_PAID,
        ]);
    }

    private function resolveDriver(Request $request): Driver
    {
        abort_unless($request->user()->role === 'driver', 403, 'Only drivers can pay commission.');

        /** @var Driver|null $driver */
        $driver = Driver::query()->where('user_id', $request->user()->id)->first();

        abort_if($driver === null, 404, 'Driver profile not found.');

        return $driver;
    }
}
