<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\Booking;
use App\Models\UserReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserReviewController extends BaseController
{
    public function storeClientReview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,webm|max:20480',
        ]);

        $professional = $request->user('professional-api');
        $booking = Booking::where('id', $validated['booking_id'])
            ->where('professional_id', $professional->id)
            ->whereNotNull('customer_id')
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            return $this->error('Booking is not eligible for reviewing this client.', 403);
        }

        if (UserReview::where('booking_id', $booking->id)->where('reviewer_id', $professional->id)->exists()) {
            return $this->error('You have already submitted a user review for this booking.', 422);
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('user-reviews/videos', 'public');
        }

        $review = UserReview::create([
            'booking_id' => $booking->id,
            'reviewer_id' => $professional->id,
            'reviewed_id' => $booking->customer_id,
            'reviewer_role' => 'professional',
            'reviewed_role' => 'client',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'content_type' => $videoPath ? 'video' : 'text',
            'video_path' => $videoPath,
            'status' => 'Pending',
        ]);

        return $this->success($review, 'Client review submitted successfully.', 201);
    }
}
