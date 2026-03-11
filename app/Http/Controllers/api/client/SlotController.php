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

        // 1. Get all active professionals in this city
        $professionals = \App\Models\Professional::where('city', $city)
            ->where('status', 'Active')
            ->where('verification', 'Verified')
            ->get();

        $totalProfessionals = $professionals->count();

        // If no professionals in this city, no slots available
        if ($totalProfessionals === 0) {
            return $this->success([], 'No available professionals in the specified city.');
        }

        // Calculate specific capacities for each time period
        $morningCapacity = 0;
        $afternoonCapacity = 0;
        $eveningCapacity = 0;

        foreach ($professionals as $pro) {
            $wh = $pro->working_hours ?? [];
            if (($wh['morning_slot'] ?? true) === true) $morningCapacity++;
            if (($wh['afternoon_slot'] ?? true) === true) $afternoonCapacity++;
            if (($wh['evening_slot'] ?? false) === true) $eveningCapacity++;
        }

        // Standard slots for the salon (6 AM to 11 PM)
        $allSlots = [
            'Morning' => ['06:00 AM', '07:00 AM', '08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM'],
            'Afternoon' => ['12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM'],
            'Evening' => ['04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM']
        ];

        // 2. Count occupied slots from Bookings (not Cancelled) in this city
        $bookingCounts = \App\Models\Booking::whereDate('date', $date)
            ->where('city', $city)
            ->where('status', '!=', 'Cancelled')
            ->select('slot', \DB::raw('count(*) as count'))
            ->groupBy('slot')
            ->pluck('count', 'slot')
            ->toArray();

        // 3. Filter available slots: Only lock if Bookings >= Capacity for that specific time period
        $availableSlots = [];
        foreach ($allSlots as $period => $slots) {
            $periodCapacity = 0;
            if ($period === 'Morning') $periodCapacity = $morningCapacity;
            if ($period === 'Afternoon') $periodCapacity = $afternoonCapacity;
            if ($period === 'Evening') $periodCapacity = $eveningCapacity;

            if ($periodCapacity > 0) {
                foreach ($slots as $slot) {
                    $totalOccupied = $bookingCounts[$slot] ?? 0;
                    if ($totalOccupied < $periodCapacity) {
                        $availableSlots[] = $slot;
                    }
                }
            }
        }

        return $this->success($availableSlots, 'Available slots retrieved successfully.');
    }
}
