<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        // Standard slots for the salon (this could be dynamic based on professional availability)
        $slots = [
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

        // For now, returning fixed slots. In a real scenario, we'd check against bookings.
        return $this->success($slots, 'Available slots retrieved successfully.');
    }
}
