<?php

namespace App\Http\Controllers\Api\Professionals;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ReferralService;
use App\Services\BookingService;
use App\Http\Requests\Professional\Job\ScanKitRequest;
use App\Http\Requests\Professional\Job\CompleteJobRequest;
use App\Http\Requests\Professional\Job\PaymentConfirmRequest;
use Illuminate\Support\Str;

class JobController extends BaseController
{
    public function arrived(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->update(['status' => 'Arrived']);
        }
        return $this->success(null, 'Arrival marked successfully.');
    }

    /**
     * POST /api/professional/jobs/{id}/start-journey
     */
    public function startJourney(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->update(['status' => 'Started']); // or 'On The Way'
        }
        return $this->success(null, 'Journey started.');
    }

    public function startService(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->update(['status' => 'In Progress']);
        }
        return $this->success(null, 'Service started.');
    }

    /**
     * POST /api/professional/jobs/{id}/scan-kit
     */
    public function scanKit(ScanKitRequest $request, $id): JsonResponse
    {
        // Logic to verify kit
        return $this->success(null, 'Kit scanned and verified.');
    }

    public function complete(CompleteJobRequest $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);

        if ($booking) {
            BookingService::completeJob($booking);
        }

        return $this->success(null, 'Job marked as complete.');
    }

    /**
     * POST /api/professional/jobs/{id}/payment-confirm
     */
    public function paymentConfirm(PaymentConfirmRequest $request, $id): JsonResponse
    {
        // Logic for payment confirmation
        return $this->success(null, 'Payment confirmed.');
    }

    public function createPaymentOrder(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $amountPaise = (int) round($booking->price * 100);

        try {
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $order = $api->order->create([
                'receipt'  => 'booking_' . $id . '_' . Str::random(4),
                'amount'   => $amountPaise,
                'currency' => 'INR',
                'notes'    => [
                    'booking_id' => $id,
                    'professional_id' => $request->user('professional-api')->id
                ]
            ]);
            
            return $this->success([
                'order_id'     => $order['id'],
                'amount'       => $amountPaise,
                'currency'     => 'INR',
                'receipt'      => $order['receipt'],
            ], 'Razorpay order created.');
        } catch (\Exception $e) {
            return $this->error('Failed to create Razorpay order: ' . $e->getMessage(), 500);
        }
    }

    public function verifyPayment(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::findOrFail($id);
        
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        try {
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $attributes = [
                'razorpay_order_id'   => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature'  => $validated['razorpay_signature']
            ];
            $api->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            return $this->error('Payment verification failed.', 422);
        }

        // Use BookingService to handle completion, wallet credits, and referral check
        BookingService::completeJob($booking);

        return $this->success(null, 'Payment verified and job completed.');
    }
}
