<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'all');
        $customer = $this->guard()->user();

        $query = $customer->bookingsRel();

        if ($status === 'Upcoming') {
            $query->whereIn('status', ['unassigned', 'pending', 'confirmed', 'assigned', 'in_progress']);
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $bookings = $query
            ->with(['customer', 'order', 'service', 'variant.service', 'professional', 'package'])
            ->latest()
            ->get();

        return $this->success(BookingResource::collection($bookings), 'Bookings retrieved successfully.');
    }

    public function show(Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $booking->load(['customer', 'order', 'service', 'variant.service', 'professional', 'package']);

        return $this->success(new BookingResource($booking), 'Booking details retrieved successfully.');
    }

    public function cancel(Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return $this->error('Booking cannot be cancelled in current status: ' . $booking->status, 422);
        }

        $booking->update(['status' => 'cancelled']);

        return $this->success(new BookingResource($booking->fresh(['customer', 'order', 'service', 'variant.service', 'professional', 'package'])), 'Booking cancelled successfully.');
    }

    public function reschedule(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|string',
        ]);

        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return $this->error('Cannot reschedule a ' . strtolower($booking->status) . ' booking.', 422);
        }

        $city = $booking->city ?? 'Mumbai';
        $professionals = \App\Models\Professional::where('city', $city)
            ->where('status', 'Active')
            ->where('verification', 'Verified')
            ->get();
            
        $capacity = 0;
        
        // Determine slot period
        $slot = $validated['slot'];
        $isMorning = str_contains($slot, 'AM') || $slot === '12:00 PM'; // 12 PM is afternoon, wait
        
        $period = '';
        if (in_array($slot, ['06:00 AM', '07:00 AM', '08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM'])) {
            $period = 'morning';
        } elseif (in_array($slot, ['12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM'])) {
            $period = 'afternoon';
        } else {
            $period = 'evening';
        }

        foreach ($professionals as $pro) {
            $wh = $pro->working_hours ?? [];
            if ($period === 'morning' && ($wh['morning_slot'] ?? true) === true) $capacity++;
            if ($period === 'afternoon' && ($wh['afternoon_slot'] ?? true) === true) $capacity++;
            if ($period === 'evening' && ($wh['evening_slot'] ?? false) === true) $capacity++;
        }

        $occupied = \App\Models\Booking::whereDate('date', $validated['date'])
            ->where('slot', $validated['slot'])
            ->where('city', $city)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $booking->id)
            ->count();

        if ($occupied >= $capacity || $capacity === 0) {
            return $this->error('This slot is already full or unavailable. Please choose another time.', 422);
        }

        $booking->update([
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ]);

        return $this->success(new BookingResource($booking->fresh(['customer', 'order', 'service', 'variant.service', 'professional', 'package'])), 'Booking rescheduled successfully.');
    }
}
