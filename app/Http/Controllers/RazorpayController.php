<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PlanActivation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Razorpay\Api\Api;
use Throwable;

class RazorpayController extends Controller
{
    public function createOrder(Request $request, PlanActivation $activation): JsonResponse
    {
        try {
            $quote = $activation->quoteFor($request->all());
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $paise = $quote['rupees'] * 100;

        if ($paise < 100) {
            return response()->json(['error' => 'Amount is too small to charge.'], 400);
        }

        $keyId = (string) config('services.razorpay.key');
        $keySecret = (string) config('services.razorpay.secret');
        $useDummy = config('services.razorpay.dummy') || $keyId === '' || $keySecret === '';

        if ($useDummy) {
            $payment = $activation->dummyPurchase($request->user(), $request->all());

            return response()->json([
                'dummy' => true,
                'payment_id' => $payment->payment_id,
                'plan' => $payment->plan_name,
            ]);
        }

        $payment = $activation->start($request->user(), $request->all());

        try {
            $api = new Api($keyId, $keySecret);
            $order = $api->order->create([
                'amount' => $paise,
                'currency' => 'INR',
                'receipt' => 'tfi_'.$payment->id,
                'notes' => array_merge(
                    [
                        'plan' => $quote['name'],
                        'customer_id' => (string) $request->user()->getAuthIdentifier(),
                        'payment_row' => (string) $payment->id,
                    ],
                    $quote['labels']
                ),
            ]);
        } catch (Throwable $e) {
            Log::error('Razorpay create-order failed', ['exception' => $e]);
            $payment->delete();

            return response()->json(['error' => 'Could not start the payment. Please try again.'], 500);
        }

        $payment->forceFill(['order_id' => $order['id']])->save();

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

    public function verify(Request $request, PlanActivation $activation): JsonResponse
    {
        $orderId = $request->input('razorpay_order_id');
        $paymentId = $request->input('razorpay_payment_id');
        $signature = $request->input('razorpay_signature');

        if (! is_string($orderId) || ! is_string($paymentId) || ! is_string($signature)) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        $keySecret = (string) config('services.razorpay.secret');

        if ($keySecret === '') {
            return response()->json(['error' => 'Payments are not configured.'], 503);
        }

        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $keySecret);

        if (! hash_equals($expected, $signature)) {
            return response()->json(['error' => 'Payment verification failed.'], 400);
        }

        $payment = Payment::query()
            ->where('order_id', $orderId)
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->first();

        if (! $payment) {
            return response()->json(['error' => 'This payment does not belong to your account.'], 403);
        }

        try {
            $activation->complete($payment, $paymentId);
            $this->recordPaid([
                'source' => 'verify',
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'customer_id' => $request->user()->getAuthIdentifier(),
                'amount' => $payment->amount_inr * 100,
                'currency' => $payment->currency,
                'at' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Plan activation after verify failed', ['exception' => $e]);

            return response()->json(['error' => 'Could not confirm the payment. Please contact support before trying again.'], 502);
        }

        return response()->json(['ok' => true]);
    }

    public function webhook(Request $request, PlanActivation $activation): Response
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
            $entity = $event['payload']['payment']['entity'] ?? null;

            if (is_array($entity)) {
                $this->recordPaid([
                    'source' => 'webhook',
                    'order_id' => $entity['order_id'] ?? null,
                    'payment_id' => $entity['id'] ?? null,
                    'amount' => $entity['amount'] ?? null,
                    'currency' => $entity['currency'] ?? null,
                    'email' => $entity['email'] ?? null,
                    'contact' => $entity['contact'] ?? null,
                    'notes' => $entity['notes'] ?? null,
                    'at' => now()->toIso8601String(),
                ]);

                $orderId = $entity['order_id'] ?? null;
                if (is_string($orderId) && $orderId !== '') {
                    $payment = Payment::query()->where('order_id', $orderId)->first();
                    if ($payment && ! $payment->isPaid()) {
                        try {
                            $activation->complete($payment, is_string($entity['id'] ?? null) ? $entity['id'] : null);
                        } catch (Throwable $e) {
                            Log::error('Plan activation after webhook failed', ['exception' => $e]);
                        }
                    }
                }
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
