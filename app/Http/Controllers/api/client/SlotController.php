<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends BaseController
{
    protected function guard()
    {
        return \Illuminate\Support\Facades\Auth::guard('api');
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'city' => 'nullable|string',
        ]);

        $date = $validated['date'];
        $customer = $this->guard()->user();
        $city = $validated['city'] ?? ($customer ? $customer->city : 'Mumbai');

        // 1. Get total professional capacity for this city
        $capacity = \App\Models\Professional::where('city', $city)
            ->where('status', 'Active')
            ->where('verification', 'Verified')
            ->count();

        // If no professionals in this city, no slots available
        if ($capacity === 0) {
            return $this->success([], 'No available professionals in the specified city.');
        }

        // Standard slots for the salon (10 AM to 8 PM)
        $allSlots = [
            '10:00 AM',
            '11:00 AM',
            '12:00 PM',
            '01:00 PM',
            '02:00 PM',
            '03:00 PM',
            '04:00 PM',
            '05:00 PM',
            '06:00 PM',
            '07:00 PM',
            '08:00 PM'
        ];

        // 2. Count occupied slots from Bookings (not Cancelled) in this city
        $bookingCounts = \App\Models\Booking::whereDate('date', $date)
            ->where('city', $city)
            ->where('status', '!=', 'Cancelled')
            ->select('slot', \DB::raw('count(*) as count'))
            ->groupBy('slot')
            ->pluck('count', 'slot')
            ->toArray();

        // 3. Filter available slots: Only lock if Bookings >= Capacity
        $availableSlots = array_values(array_filter($allSlots, function ($slot) use ($bookingCounts, $capacity) {
            $totalOccupied = $bookingCounts[$slot] ?? 0;
            return $totalOccupied < $capacity;
        }));

        return $this->success($availableSlots, 'Available slots retrieved successfully.');
    }
}
