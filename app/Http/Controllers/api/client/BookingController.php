<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
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
        $status = $request->query('status', 'Upcoming'); // Upcoming, Completed, Cancelled
        $customer = $this->guard()->user();

        $query = $customer->bookingsRel();

        if ($status === 'Upcoming') {
            $query->whereIn('status', ['Unassigned', 'Pending', 'Confirmed', 'Assigned', 'In Progress']);
        } elseif ($status === 'Completed') {
            $query->where('status', 'Completed');
        } elseif ($status === 'Cancelled') {
            $query->where('status', 'Cancelled');
        }

        $bookings = $query->with(['service', 'professional'])->latest()->get();

        return $this->success(BookingResource::collection($bookings), 'Bookings retrieved successfully.');
    }

    public function show(Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $booking->load(['service', 'professional', 'package']);
        return $this->success(new BookingResource($booking), 'Booking details retrieved successfully.');
    }

    public function cancel(Booking $booking): JsonResponse
    {
        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        if ($booking->status === 'Completed' || $booking->status === 'Cancelled') {
            return $this->error('Booking cannot be cancelled in current status: ' . $booking->status, 422);
        }

        $booking->update(['status' => 'Cancelled']);

        return $this->success(new BookingResource($booking), 'Booking cancelled successfully.');
    }
}
