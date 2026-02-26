<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Booking;
use App\Models\Professional;
use App\Http\Resources\Api\BookingResource;
use App\Http\Resources\Api\ProfessionalResource;
use App\Http\Requests\Api\Admin\StoreAssignmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends BaseController
{
    /**
     * Display a listing of assignable bookings and active professionals.
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['customer', 'service', 'package'])
            ->whereNotIn('status', ['Cancelled', 'Completed', 'Assigned'])
            ->latest()
            ->get();

        $professionals = Professional::where('status', 'Active')
            ->latest()
            ->get();

        return $this->success([
            'bookings'      => BookingResource::collection($bookings),
            'professionals' => ProfessionalResource::collection($professionals),
        ], 'Assignable bookings and professionals retrieved.');
    }

    /**
     * Assign a professional to a booking.
     */
    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $booking = Booking::findOrFail($request->booking_id);
        
        $booking->update([
            'professional_id' => $request->professional_id,
            'status'          => 'Assigned',
        ]);

        return $this->success(
            new BookingResource($booking->load(['customer', 'service', 'package', 'professional'])),
            'Professional assigned successfully.'
        );
    }
}
