<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\BookingResource;
use App\Models\Booking;
use Illuminate\Support\Carbon;
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

        if (!$booking->canCancel()) {
            return $this->error('Booking cannot be cancelled in current status: ' . $booking->status, 422);
        }

        $booking->applyStatusTransition('cancelled', [
            'current_step' => 'cancelled',
        ]);

        return $this->success(new BookingResource($booking->fresh(['customer', 'order', 'service', 'variant.service', 'professional', 'package'])), 'Booking cancelled successfully.');
    }

    public function reschedule(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $maxRescheduleDate = now()->addDays(7)->toDateString();

        $validated = $request->validate([
            'new_date' => 'required|date|after_or_equal:today|before_or_equal:' . $maxRescheduleDate,
            'new_time_slot' => 'required|string',
        ]);

        if (!$booking->canReschedule()) {
            return $this->error('Booking cannot be rescheduled in current status: ' . $booking->status, 422);
        }

        if (!$booking->hasRemainingRescheduleAttempt()) {
            return $this->error('Booking can only be rescheduled once.', 422);
        }

        $city = trim((string) ($booking->city ?? ''));
        if ($city === '') {
            return $this->error('Booking city is missing. Please contact support.', 422);
        }
        $professionals = \App\Models\Professional::whereRaw('TRIM(city) = ?', [$city])
            ->where('status', 'Active')
            ->where('verification', 'Verified')
            ->get();
            
        $capacity = 0;
        
        $slot = $validated['new_time_slot'];
        try {
            $slotTime = Carbon::createFromFormat('h:i A', $slot);
        } catch (\Throwable $e) {
            return $this->error('Selected time slot format is invalid.', 422);
        }

        $minutesOfDay = ($slotTime->hour * 60) + $slotTime->minute;
        if ($minutesOfDay < (12 * 60)) {
            $period = 'morning';
        } elseif ($minutesOfDay < (16 * 60)) {
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

        $occupied = \App\Models\Booking::whereDate('date', $validated['new_date'])
            ->where('slot', $validated['new_time_slot'])
            ->where('city', $city)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $booking->id)
            ->count();

        if ($occupied >= $capacity || $capacity === 0) {
            return $this->error('This slot is already full or unavailable. Please choose another time.', 422);
        }

        $existingHistory = $booking->rescheduleHistory();

        $booking->update([
            'date' => $validated['new_date'],
            'slot' => $validated['new_time_slot'],
            'meta' => array_merge($booking->meta ?? [], [
                'reschedule_history' => array_merge(
                    $existingHistory,
                    [[
                        'old_date' => optional($booking->date)->format('Y-m-d'),
                        'old_slot' => $booking->slot,
                        'new_date' => Carbon::parse($validated['new_date'])->format('Y-m-d'),
                        'new_slot' => $validated['new_time_slot'],
                        'rescheduled_at' => now()->toIso8601String(),
                    ]]
                ),
            ]),
        ]);

        return $this->success(new BookingResource($booking->fresh(['customer', 'order', 'service', 'variant.service', 'professional', 'package'])), 'Booking rescheduled successfully.');
    }
}
