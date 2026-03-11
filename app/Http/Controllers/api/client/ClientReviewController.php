<?php

namespace App\Http\Controllers\api\client;

use App\Models\Booking;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientReviewController extends Controller
{
    /**
     * Fetch paginated reviews for a specific service.
     */
    public function index($serviceId)
    {
        $reviews = Review::with('customer')
            ->whereHas('booking', fn($q) => $q->where('service_id', $serviceId))
            ->where('status', 'Approved')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Submit a review for a specific booking.
     */
    public function store(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'video_path' => 'nullable|string' // Use relative path logic
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Security: Must belong to customer and be completed
        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $request->user()->id)
            ->whereIn('status', ['Completed', 'Served'])
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or booking not completed.'], 403);
        }

        // Unique constraint check (redundancy for UX)
        if (Review::where('booking_id', $bookingId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Review already submitted.'], 400);
        }

        DB::beginTransaction();
        try {
            $review = Review::create([
                'booking_id' => $bookingId,
                'customer_id' => $request->user()->id,
                'service_id' => $booking->service_id, // Explicitly linking service for easier lookup
                'rating' => $request->rating,
                'comment' => $request->comment,
                'video_path' => $request->video_path,
                'status' => 'Pending',
                'review_type' => 'Service'
            ]);

            DB::commit();

            // Note: Aggregation should idealistically happen on APPROVAL.
            // If the user wants immediate feedback for testing, we'll implement that in Admin.
            // For now, we follow the 'Pending' rule.

            return response()->json([
                'success' => true,
                'message' => 'Review submitted and pending approval.',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
