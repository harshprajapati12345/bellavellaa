<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayWebhookController extends Controller
{
    /**
     * POST /api/razorpay/webhook
     * The Industry-Standard Source of Truth for all Online Payments.
     */
    public function handle(Request $request)
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        try {
            // 🛡️ SECURITY: Real Signature Verification
            if ($webhookSecret) {
                $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $api->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
            }
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Webhook: Invalid Signature rejected', ['signature' => $webhookSignature]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;
        $payloadData = $data['payload'] ?? null;

        if (!$event || !$payloadData) {
            return response()->json(['status' => 'empty_payload']);
        }

        Log::info('Razorpay Webhook: Processing event', ['event' => $event]);

        // 🔗 ORCHESTRATION: Delegate to Atomic PaymentService
        switch ($event) {
            case 'payment.captured':
                $entity = $payloadData['payment']['entity'];
                PaymentService::processCapture(
                    $entity['order_id'], 
                    $entity['id'], 
                    $entity
                );
                break;

            case 'payment.failed':
                $entity = $payloadData['payment']['entity'];
                PaymentService::processFailure(
                    $entity['order_id'], 
                    $entity
                );
                break;

            case 'payment.authorized':
                Log::info('Razorpay: Payment Authorized (Pending Capture)', ['payment_id' => $payloadData['payment']['entity']['id']]);
                break;
        }

        return response()->json(['status' => 'ok']);
    }
}
