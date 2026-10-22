<?php

namespace App\Http\Controllers;

use App\Services\PlanPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Razorpay\Api\Api;
use Throwable;

class RazorpayController extends Controller
{
    public function createOrder(Request $request, PlanPricing $pricing): JsonResponse
    {
        $keyId = (string) config('services.razorpay.key');
        $keySecret = (string) config('services.razorpay.secret');

        if ($keyId === '' || $keySecret === '') {
            return response()->json(['error' => 'Payments are not configured.'], 503);
        }

        try {
            $quote = $pricing->priceFor($request->all());
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $paise = $quote['rupees'] * 100;

        if ($paise < 100) {
            return response()->json(['error' => 'Amount is too small to charge.'], 400);
        }

        try {
            $api = new Api($keyId, $keySecret);
            $order = $api->order->create([
                'amount' => $paise,
                'currency' => 'INR',
                'receipt' => 'tfi_'.now()->getTimestampMs(),
                'notes' => array_merge(
                    [
                        'plan' => $quote['name'],
                        'customer_id' => (string) $request->user()->getAuthIdentifier(),
                    ],
                    $quote['labels']
                ),
            ]);
        } catch (Throwable $e) {
            Log::error('Razorpay create-order failed', ['exception' => $e]);

            return response()->json(['error' => 'Could not start the payment. Please try again.'], 500);
        }

        return response()->json([
            'key_id' => $keyId,
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'description' => $quote['name'].(isset($quote['labels']['session'])
                ? ' — '.$quote['labels']['session'].', '.$quote['labels']['couple']
                : ''),
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $orderId = $request->input('razorpay_order_id');
        $paymentId = $request->input('razorpay_payment_id');
        $signature = $request->input('razorpay_signature');

        if (! is_string($orderId) || ! is_string($paymentId) || ! is_string($signature)) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        $keyId = (string) config('services.razorpay.key');
        $keySecret = (string) config('services.razorpay.secret');

        if ($keyId === '' || $keySecret === '') {
            return response()->json(['error' => 'Payments are not configured.'], 503);
        }

        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $keySecret);

        if (! hash_equals($expected, $signature)) {
            return response()->json(['error' => 'Payment verification failed.'], 400);
        }

        try {
            $api = new Api($keyId, $keySecret);
            $order = $api->order->fetch($orderId);

            if ((string) ($order['notes']['customer_id'] ?? '') !== (string) $request->user()->getAuthIdentifier()) {
                return response()->json(['error' => 'This payment does not belong to your account.'], 403);
            }

            if (($order['status'] ?? null) !== 'paid') {
                return response()->json(['error' => 'The payment has not been completed.'], 400);
            }

            $this->recordPaid([
                'source' => 'verify',
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'customer_id' => $request->user()->getAuthIdentifier(),
                'amount' => $order['amount'] ?? null,
                'currency' => $order['currency'] ?? null,
                'notes' => $order['notes'] ?? null,
                'at' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Razorpay order fetch after verify failed', ['exception' => $e]);

            return response()->json(['error' => 'Could not confirm the payment. Please contact support before trying again.'], 502);
        }

        return response()->json(['ok' => true]);
    }

    public function webhook(Request $request): Response
    {
        $secret = (string) config('services.razorpay.webhook_secret');

        if ($secret === '') {
            return response()->noContent(503);
        }

        $payload = $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature', '');
        $expected = hash_hmac('sha256', $payload, $secret);

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            return response()->noContent(400);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response()->noContent(400);
        }

        if (($event['event'] ?? null) === 'order.paid' || ($event['event'] ?? null) === 'payment.captured') {
            $payment = $event['payload']['payment']['entity'] ?? null;

            if (is_array($payment)) {
                $this->recordPaid([
                    'source' => 'webhook',
                    'order_id' => $payment['order_id'] ?? null,
                    'payment_id' => $payment['id'] ?? null,
                    'amount' => $payment['amount'] ?? null,
                    'currency' => $payment['currency'] ?? null,
                    'email' => $payment['email'] ?? null,
                    'contact' => $payment['contact'] ?? null,
                    'notes' => $payment['notes'] ?? null,
                    'at' => now()->toIso8601String(),
                ]);
            }
        }

        return response()->noContent(200);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function recordPaid(array $entry): void
    {
        $path = storage_path('app/paid-orders.jsonl');
        file_put_contents($path, json_encode($entry)."\n", FILE_APPEND | LOCK_EX);
    }
}
