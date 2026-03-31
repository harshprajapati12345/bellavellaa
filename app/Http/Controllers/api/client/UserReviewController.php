<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\Booking;
use App\Models\UserReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function storeProfessionalReview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,webm|max:20480',
        ]);

        $customerId = $this->guard()->id();
        $booking = Booking::where('id', $validated['booking_id'])
            ->where('customer_id', $customerId)
            ->whereNotNull('professional_id')
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            return $this->error('Booking is not eligible for reviewing this professional.', 403);
        }

        if (UserReview::where('booking_id', $booking->id)->where('reviewer_id', $customerId)->exists()) {
            return $this->error('You have already submitted a user review for this booking.', 422);
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('user-reviews/videos', 'public');
        }

        $review = UserReview::create([
            'booking_id' => $booking->id,
            'reviewer_id' => $customerId,
            'reviewed_id' => $booking->professional_id,
            'reviewer_role' => 'client',
            'reviewed_role' => 'professional',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'content_type' => $videoPath ? 'video' : 'text',
            'video_path' => $videoPath,
            'status' => 'Pending',
        ]);

        return $this->success($review, 'Professional review submitted successfully.', 201);
    }
}
