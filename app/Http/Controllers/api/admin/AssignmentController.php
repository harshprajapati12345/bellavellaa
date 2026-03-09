<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Booking;
use App\Models\Professional;
use App\Http\Resources\Api\BookingResource;
use App\Http\Resources\Api\ProfessionalResource;
use App\Http\Requests\Api\Admin\StoreAssignmentRequest;
use App\Models\ProfessionalNotification;
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

        // Create notification for the professional
        ProfessionalNotification::create([
            'professional_id' => $request->professional_id,
            'type'            => 'booking_assigned',
            'title'           => 'New Booking Assigned!',
            'body'            => 'You have a new booking request from ' . ($booking->customer->name ?? 'Customer'),
            'data'            => [
                'booking_id'  => $booking->id,
                'client_name' => $booking->customer->name ?? 'Customer',
                'service'     => $booking->service->name ?? 'Service',
                'location'    => $booking->address ?? 'Nearby',
                'price'       => $booking->price,
            ],
        ]);

        return $this->success(
            new BookingResource($booking->load(['customer', 'service', 'package', 'professional'])),
            'Professional assigned successfully.'
        );
    }
}
