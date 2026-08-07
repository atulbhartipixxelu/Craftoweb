<?php

namespace App\Http\Controllers;

use App\Services\CommissionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionPaymentCallbackController extends Controller
{
    public function phonePeCallback(Request $request, CommissionPaymentService $payments): JsonResponse
    {
        $encoded = (string) $request->input('response', '');
        if ($encoded === '') {
            return response()->json(['success' => false], 400);
        }

        $decoded = json_decode(base64_decode($encoded), true);
        $merchantTransactionId = $decoded['data']['merchantTransactionId'] ?? null;

        if (! is_string($merchantTransactionId) || $merchantTransactionId === '') {
            return response()->json(['success' => false], 400);
        }

        $payments->completeByMerchantTransactionId($merchantTransactionId);

        return response()->json(['success' => true]);
    }
}
