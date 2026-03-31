<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'photo' => 'nullable|image|max:5120', // 5MB
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:20480', // 20MB
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        if ($booking->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        // Check if already reviewed
        if ($booking->review) {
            return $this->error('You have already reviewed this booking.', 422);
        }

        $reviewData = [
            'booking_id' => $booking->id,
            'customer_id' => $this->guard()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'review_type' => 'text',
            'status' => 'Approved', // Auto-approving for test/demo
        ];

        if ($booking->customer) {
            $reviewData['customer_name'] = $booking->customer->name;
            $reviewData['customer_avatar'] = $booking->customer->avatar;
        }

        if ($request->hasFile('photo')) {
            $reviewData['image_path'] = $request->file('photo')->store('reviews/photos', 'public');
        }

        if ($request->hasFile('video')) {
            $reviewData['video_path'] = $request->file('video')->store('reviews/videos', 'public');
            $reviewData['review_type'] = 'video';
        }

        $review = Review::create($reviewData);

        return $this->success($review, 'Review submitted successfully.');
    }
}
