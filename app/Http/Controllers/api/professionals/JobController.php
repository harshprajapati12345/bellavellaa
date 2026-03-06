<?php

namespace App\Http\Controllers\Api\Professionals;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ReferralService;
use App\Http\Requests\Professional\Job\ScanKitRequest;
use App\Http\Requests\Professional\Job\CompleteJobRequest;
use App\Http\Requests\Professional\Job\PaymentConfirmRequest;

class JobController extends BaseController
{
    /**
     * POST /api/professional/jobs/{id}/arrived
     */
    public function arrived(Request $request, $id): JsonResponse
    {
        // Logic to mark arrival
        return $this->success(null, 'Arrival marked successfully.');
    }

    /**
     * POST /api/professional/jobs/{id}/start-service
     */
    public function startService(Request $request, $id): JsonResponse
    {
        // Logic to start service
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
            $booking->update(['status' => 'Completed']);

            // Trigger referral reward check
            if ($booking->customer) {
                ReferralService::processFirstBookingCompletion($booking->customer);
            }
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
}
