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
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;

class JobController extends BaseController
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Checks if a status transition is allowed.
     */
    private function canTransition(string $current, string $next): bool
    {
        $map = config('booking.transitions');
        return in_array($next, $map[$current] ?? []);
    }

    private function validateOwnership(\App\Models\Booking $booking, Request $request): void
    {
        if ($booking->professional_id !== $request->user('professional-api')->id) {
            throw new \Exception('Unauthorized access.', 403);
        }
    }

    private function resolveBookingPayablePaise(\App\Models\Order $order, \App\Models\Booking $booking): int
    {
        $bookingAmountPaise = (int) round(((float) $booking->price) * 100);
        $rawCandidates = [
            $order->total_paise ?? null,
            $order->final_payable_paise ?? null,
        ];

        foreach ($rawCandidates as $rawAmount) {
            if ($rawAmount === null || $rawAmount === '') {
                continue;
            }

            $numericAmount = (float) $rawAmount;
            if ($numericAmount <= 0) {
                continue;
            }

            $amountPaise = (int) round($numericAmount);
            $looksLikeLegacyRupees = $booking->price > 0
                && abs($numericAmount - (float) $booking->price) < 0.01
                && abs($amountPaise - $bookingAmountPaise) > 1;

            if ($looksLikeLegacyRupees) {
                $normalizedAmount = (int) round($numericAmount * 100);
                Log::warning('Normalized legacy rupee amount before collecting professional payment.', [
                    'order_id' => $order->id,
                    'booking_id' => $booking->id,
                    'raw_amount' => $rawAmount,
                    'normalized_amount_paise' => $normalizedAmount,
                ]);

                return $normalizedAmount;
            }

            return $amountPaise;
        }

        return max($bookingAmountPaise, 0);
    }

    public function arrived(Request $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if (!$this->canTransition($booking->status, 'arrived')) {
                return $this->error('Invalid state transition from ' . $booking->status . ' to arrived.', 400);
            }

            // ✅ PERSIST STATE
            $booking->update([
                'status' => 'arrived',
                'current_step' => 'kit_scan',
            ]);

            $this->sendDashboardUpdate($booking);
            
            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Arrival marked successfully.');
        } catch (\Throwable $e) {
            \Log::error('JobController::arrived error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while marking arrival.', $code);
        }
    }

    /**
     * POST /api/professional/jobs/{id}/start-journey
     */
    public function startJourney(Request $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if (!$this->canTransition($booking->status, 'on_the_way')) {
                return $this->error('Invalid state transition from ' . $booking->status . ' to on_the_way.', 400);
            }

            // ✅ PERSIST STATE
            $booking->update([
                'status' => 'on_the_way',
                'current_step' => 'journey',
            ]);

            $this->sendDashboardUpdate($booking);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Journey started successfully.');
        } catch (\Throwable $e) {
            \Log::error('JobController::startJourney error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while starting journey.', $code);
        }
    }

    public function startService(Request $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if (!$this->canTransition($booking->status, 'in_progress')) {
                return $this->error('Invalid state transition from ' . $booking->status . ' to in_progress.', 400);
            }

            // ✅ PERSIST STATE
            $booking->update([
                'status' => 'in_progress',
                'current_step' => 'service',
            ]);

            $this->sendDashboardUpdate($booking);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Service started.');
        } catch (\Throwable $e) {
            Log::error('JobController::startService error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while starting service.', $code);
        }
    }

    public function finishService(Request $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if (!$this->canTransition($booking->status, 'payment_pending')) {
                return $this->error('Invalid state transition from ' . $booking->status . ' to payment_pending.', 400);
            }

            // ✅ PERSIST STATE
            $booking->update([
                'status' => 'payment_pending',
                'current_step' => 'payment',
            ]);

            $this->sendDashboardUpdate($booking);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Service finished, awaiting payment.');
        } catch (\Throwable $e) {
            Log::error('JobController::finishService error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while finishing service.', $code);
        }
    }

    /**
     * POST /api/professional/jobs/{id}/scan-kit
     */
    public function scanKit(ScanKitRequest $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if (!$this->canTransition($booking->status, 'scan_kit')) {
                return $this->error('Invalid state transition from ' . $booking->status . ' to scan_kit.', 400);
            }

            // ✅ PERSIST STATE
            $booking->update([
                'status' => 'scan_kit',
                'current_step' => 'kit_scan',
            ]);

            $this->sendDashboardUpdate($booking);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Kit scanned and verified.');
        } catch (\Throwable $e) {
            Log::error('JobController::scanKit error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while scanning kit.', $code);
        }
    }

    public function complete(CompleteJobRequest $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            // ✅ STATE VALIDATION (Strict Transition)
            if ($booking->status === 'completed') {
                return $this->success(new \App\Http\Resources\Api\BookingResource($booking), 'Job already completed.');
            }

            // ✅ EXECUTE COMPLETION LOGIC (Production Shield)
            \App\Services\BookingService::completeJob($booking);
            
            // Mark professional as idle in Firestore and set isActive to false
            $this->firebase->pushJobToFirestore([
                'professional_id' => $booking->professional_id,
                'booking_id'      => $booking->id,
                'status'          => 'idle',
                'current_step'    => 'completed',
                'isActive'        => false,
                'updated_at'      => time(),
            ]);

            $this->sendDashboardUpdate($booking);

            return $this->success(new \App\Http\Resources\Api\BookingResource($booking->fresh()), 'Job marked as complete.');
        } catch (\Throwable $e) {
            Log::error('JobController::complete error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Something went wrong while completing job.', $code);
        }
    }

    /**
     * POST /api/professional/jobs/{id}/collect-cash
     */
    public function collectCash(Request $request, $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id, $request) {
                $booking = \App\Models\Booking::with('order')->lockForUpdate()->find($id);
                if (!$booking) {
                    return $this->error('Booking not found.', 404);
                }

                $this->validateOwnership($booking, $request);

                $order = $booking->order;
                if (!$order) {
                    return $this->error('Order not found for this booking.', 404);
                }

                // Step 7.1 & 8.1: ENFORCE PAID STATUS
                if ($order->payment_status === 'SUCCESS') {
                    return $this->error('Payment is already SUCCESS for this order.', 400);
                }

                // SECURITY GUARD: Only COD orders allowed
                if ($order->payment_method !== 'cod') {
                    return $this->error('Invalid payment method for cash collection.', 400);
                }

                // Step 7.2 & 10.2: Create Auditable Payment Record
                Payment::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'payment_method' => 'COD',
                    'gateway' => 'cash',
                    'amount_paise' => $this->resolveBookingPayablePaise($order, $booking),
                    'status' => 'SUCCESS',
                    'paid_at' => now(),
                    'meta_json' => [
                        'collected_by_professional_id' => $request->user('professional-api')->id,
                        'booking_id' => $booking->id,
                        'collected_at' => now()->toIso8601String()
                    ]
                ]);

                // Step 7.3: Update Order
                $order->update([
                    'payment_status' => 'SUCCESS',
                    'status' => 'confirmed'
                ]);

                // Optional: Progress the booking step
                $booking->update(['current_step' => 'complete']);

                $this->sendDashboardUpdate($booking->fresh());

                return $this->success(null, 'Cash payment recorded as SUCCESS.');
            });
        } catch (\Exception $e) {
            Log::error('JobController::collectCash error: ' . $e->getMessage());
            return $this->error('Failed to collect cash: ' . $e->getMessage(), 500);
        }
    }

    public function createPaymentOrder(Request $request, $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id, $request) {
                $booking = \App\Models\Booking::with('order')->lockForUpdate()->find($id);
                if (!$booking) {
                    return $this->error('Booking not found.', 404);
                }

                $this->validateOwnership($booking, $request);

                $order = $booking->order;
                if (!$order) {
                    return $this->error('Order not found for this booking.', 404);
                }

                if ($order->payment_status === 'SUCCESS') {
                    return $this->error('Payment is already SUCCESS for this order.', 400);
                }

                $amountPaise = $this->resolveBookingPayablePaise($order, $booking);
                if ($amountPaise < 100) {
                    return $this->error('Calculated payable amount is below Razorpay minimum.', 422);
                }

                if (config('services.razorpay.mock')) {
                    $mockOrderId = 'order_mock_' . strtolower(Str::random(14));
                    
                    Payment::create([
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'payment_method' => 'ONLINE',
                        'gateway' => 'razorpay',
                        'gateway_order_id' => $mockOrderId,
                        'amount_paise' => $amountPaise,
                        'status' => 'PENDING',
                        'meta_json' => [
                            'collected_by_professional_id' => $request->user('professional-api')->id,
                            'booking_id' => $booking->id,
                            'is_mock' => true
                        ]
                    ]);

                    return $this->success([
                        'order_id'     => $mockOrderId,
                        'amount'       => $amountPaise,
                        'currency'     => 'INR',
                        'receipt'      => 'booking_mock_' . $id . '_' . Str::random(4),
                        'is_mock'      => true,
                    ], 'Razorpay mock order created.');
                }

                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $rzpOrder = $api->order->create([
                    'receipt'  => 'booking_' . $id . '_' . Str::random(4),
                    'amount'   => $amountPaise,
                    'currency' => 'INR',
                    'notes'    => [
                        'booking_id' => $id,
                        'professional_id' => $request->user('professional-api')->id
                    ]
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'payment_method' => 'ONLINE',
                    'gateway' => 'razorpay',
                    'gateway_order_id' => $rzpOrder['id'],
                    'amount_paise' => $amountPaise,
                    'status' => 'PENDING',
                    'meta_json' => [
                        'collected_by_professional_id' => $request->user('professional-api')->id,
                        'booking_id' => $booking->id
                    ]
                ]);
                
                return $this->success([
                    'order_id'     => $rzpOrder['id'],
                    'amount'       => $amountPaise,
                    'currency'     => 'INR',
                    'receipt'      => $rzpOrder['receipt'],
                ], 'Razorpay order created.');
            });
        } catch (\Exception $e) {
            Log::error('JobController::createPaymentOrder error: ' . $e->getMessage());
            return $this->error('Failed to create Razorpay order: ' . $e->getMessage(), 500);
        }
    }

    public function verifyPayment(Request $request, $id): JsonResponse
    {
        try {
            $booking = \App\Models\Booking::with('order')->find($id);
            if (!$booking) {
                return $this->error('Booking not found.', 404);
            }

            // ✅ OWNERSHIP CHECK
            $this->validateOwnership($booking, $request);

            $order = $booking->order;
            if (!$order) {
                return $this->error('Order not found.', 404);
            }

            // Step 8.1: ENFORCE PAID STATUS (Check backend truth)
            if ($order->payment_status === 'SUCCESS' || $booking->status === 'completed') {
                return $this->success(null, 'Payment verified or job already completed.');
            }
            
            $validated = $request->validate([
                'razorpay_payment_id' => 'required|string',
                'razorpay_order_id'   => 'required|string',
                'razorpay_signature'  => 'required|string',
            ]);

            if (!config('services.razorpay.mock')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $attributes = [
                    'razorpay_order_id'   => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature'  => $validated['razorpay_signature']
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }

            // 🛡️ SHARED ATOMIC TRUTH: Use PaymentService to process the capture.
            // Whichever hits first (Webhook or this API) will process the transaction.
            PaymentService::processCapture(
                $validated['razorpay_order_id'], 
                $validated['razorpay_payment_id'], 
                $validated
            );
            
            // Mark professional as idle in Firestore and set isActive to false
            $this->firebase->pushJobToFirestore([
                'professional_id' => $booking->professional_id,
                'booking_id'      => $booking->id,
                'status'          => 'idle',
                'current_step'    => 'completed',
                'isActive'        => false,
                'updated_at'      => time(),
            ]);

            $this->sendDashboardUpdate($booking->fresh());

            return $this->success(null, 'Payment verified and job completed.');
        } catch (\Throwable $e) {
            Log::error('JobController::verifyPayment error: ' . $e->getMessage());
            $code = $e->getCode();
            if (!is_numeric($code) || $code < 100 || $code > 599) {
                $code = 500;
            }
            return $this->error($code == 403 ? $e->getMessage() : 'Payment verification failed: ' . $e->getMessage(), $code);
        }
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
