<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Review;
use App\Http\Resources\Api\ReviewResource;
use App\Http\Requests\Api\Admin\UpdateReviewRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['customer', 'booking.service']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $reviews = $query->latest()->paginate($request->input('per_page', 15));

        return $this->success([
            'reviews'    => ReviewResource::collection($reviews),
            'pagination' => [
                'total'        => $reviews->total(),
                'count'        => $reviews->count(),
                'per_page'     => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'total_pages'  => $reviews->lastPage(),
            ]
        ], 'Reviews retrieved successfully.');
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['customer', 'booking.service']);
        return $this->success(new ReviewResource($review), 'Review retrieved successfully.');
    }

    /**
     * Update the specified review.
     */
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $review->update($request->validated());

        return $this->success(new ReviewResource($review->load(['customer', 'booking.service'])), 'Review updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return $this->success(null, 'Review deleted successfully.');
    }
}
