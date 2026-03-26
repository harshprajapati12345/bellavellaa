<?php

namespace App\Http\Controllers\api\client;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientReviewController extends Controller
{
    /**
     * Fetch paginated reviews for a specific service.
     */
    public function index(Request $request, $serviceId)
    {
        $variantId = $request->integer('service_variant_id');

        $reviews = Review::with('customer')
            ->whereHas('booking', function ($query) use ($serviceId, $variantId) {
                $query->where('service_id', $serviceId);

                if ($variantId) {
                    $query->where('service_variant_id', $variantId);
                }
            })
            ->where('status', 'Approved')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews,
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
            'video_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking is not eligible for review.'], 403);
        }

        if (Review::where('booking_id', $bookingId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Review already submitted.'], 400);
        }

        DB::beginTransaction();
        try {
            $review = Review::create([
                'booking_id' => $bookingId,
                'customer_id' => $user->id,
                'service_id' => $booking->service_id,
                'service_variant_id' => $booking->service_variant_id,
                'rating' => $request->integer('rating'),
                'comment' => $request->input('comment'),
                'video_path' => $request->input('video_path'),
                'status' => 'Pending',
                'review_type' => 'Service',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review submitted and pending approval.',
                'data' => $review,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
