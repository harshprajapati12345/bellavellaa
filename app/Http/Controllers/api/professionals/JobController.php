<?php

namespace App\Http\Controllers\Api\Professionals;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ReferralService;
use App\Services\BookingService;
use App\Http\Requests\Professional\Job\ScanKitRequest;
use App\Http\Requests\Professional\Job\CompleteJobRequest;
use App\Http\Requests\Professional\Job\PaymentConfirmRequest;
use App\Services\FirebaseService;
use Illuminate\Support\Str;
use App\Events\JobUpdate;

class JobController extends BaseController
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    public function arrived(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->applyStatusTransition('arrived', [
                'current_step' => 'kit_scan',
            ]);
            if ($booking->status !== 'scan_kit') {
                $booking->status = 'scan_kit';
                $booking->save();
            }
            $this->sendDashboardUpdate($booking);
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Arrival marked successfully. Transitioning to kit scan.');
        }
        return $this->error('Booking not found.', 404);
    }

    /**
     * POST /api/professional/jobs/{id}/start-journey
     */
    public function startJourney(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->applyStatusTransition('on_the_way', [
                'current_step' => 'journey',
            ]);
            $this->sendDashboardUpdate($booking);
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Journey started.');
        }
        return $this->error('Booking not found.', 404);
    }

    public function startService(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->applyStatusTransition('in_progress', [
                'current_step' => 'service',
            ]);
            $this->sendDashboardUpdate($booking);
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Service started.');
        }
        return $this->error('Booking not found.', 404);
    }

    public function finishService(Request $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->update([
                'status' => 'payment_pending',
                'current_step' => 'payment',
            ]);
            $this->sendDashboardUpdate($booking);
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Service finished, awaiting payment.');
        }
        return $this->error('Booking not found.', 404);
    }

    /**
     * POST /api/professional/jobs/{id}/scan-kit
     */
    public function scanKit(ScanKitRequest $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);
        if ($booking) {
            $booking->update([
                'status' => 'scan_kit',
                'current_step' => 'kit_scan',
            ]);
            $this->sendDashboardUpdate($booking);
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Kit scanned and verified.');
        }
        return $this->error('Booking not found.', 404);
    }

    public function complete(CompleteJobRequest $request, $id): JsonResponse
    {
        $booking = \App\Models\Booking::find($id);

        if ($booking) {
            \App\Services\BookingService::completeJob($booking);
            $this->sendDashboardUpdate($booking);
            
            // Mark professional as idle in Firestore and set isActive to false
            $this->firebase->pushJobToFirestore([
                'professional_id' => $booking->professional_id,
                'booking_id'      => $booking->id,
                'status'          => 'idle',
                'current_step'    => 'completed',
                'isActive'        => false,
                'updated_at'      => time(),
            ]);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Job marked as complete.');
        }

        return $this->error('Booking not found.', 404);
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
            if (config('services.razorpay.mock')) {
                return $this->success([
                    'order_id'     => 'order_mock_' . strtolower(Str::random(14)),
                    'amount'       => $amountPaise,
                    'currency'     => 'INR',
                    'receipt'      => 'booking_mock_' . $id . '_' . Str::random(4),
                    'is_mock'      => true,
                ], 'Razorpay mock order created.');
            }

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
            if (!config('services.razorpay.mock')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $attributes = [
                    'razorpay_order_id'   => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature'  => $validated['razorpay_signature']
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }
        } catch (\Exception $e) {
            return $this->error('Payment verification failed.', 422);
        }

        // Use BookingService to handle completion, wallet credits, and referral check
        BookingService::completeJob($booking);
        $this->sendDashboardUpdate($booking);

        // Mark professional as idle in Firestore and set isActive to false
        $this->firebase->pushJobToFirestore([
            'professional_id' => $booking->professional_id,
            'booking_id'      => $booking->id,
            'status'          => 'idle',
            'current_step'    => 'completed',
            'isActive'        => false,
            'updated_at'      => time(),
        ]);

        return $this->success(null, 'Payment verified and job completed.');
    }

    protected function sendDashboardUpdate($booking)
    {
        // 1. Broadcast via WebSockets (Real-Time Architecture)
        broadcast(new JobUpdate($booking));

        // 2. Send Push Notification (FCM)
        $professional = $booking->professional;
        if ($professional && $professional->fcm_token) {
            $this->firebase->sendPushNotification(
                $professional->fcm_token,
                'Job Update',
                "Job status updated to {$booking->status}",
                [
                    'type' => 'job_status_updated',
                    'booking_id' => (string)$booking->id,
                    'status' => $booking->status,
                    'current_step' => $booking->current_step,
                ]
            );
        }

        // 3. Sync Firestore as well manually
        $this->firebase->pushJobToFirestore([
            'professional_id' => $booking->professional_id,
            'booking_id'      => (string)$booking->id,
            'status'          => $booking->status,
            'current_step'    => $booking->current_step,
            'isActive'        => true,
            'updated_at'      => time(),
        ]);
    }
}
