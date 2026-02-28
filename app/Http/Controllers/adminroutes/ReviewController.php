<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['customer', 'booking.service'])->orderBy('created_at', 'desc')->get();
        $stats = [
            'total' => $reviews->count(),
            'video' => $reviews->where('review_type', 'video')->count(),
            'pending' => $reviews->where('status', 'Pending')->count(),
            'points' => $reviews->sum('points_given'),
        ];

        return view('reviews.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        $payload = ['status' => 'Approved'];

        // Automate points for video reviews only
        if ($review->review_type === 'video') {
            $payload['points_given'] = \App\Models\Setting::get('review_points_amount', 50);
        } else {
            $payload['points_given'] = 0; // No points for text reviews
        }

        $review->update($payload);
        return back()->with('success', 'Review approved successfully!' . ($review->review_type === 'video' ? ' Reward points awarded.' : ''));
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'Rejected']);
        return back()->with('success', 'Review rejected.');
    }

    public function toggleFeatured(Review $review)
    {
        $review->update(['is_featured' => !$review->is_featured]);
        return back()->with('success', 'Featured status updated!');
    }

    public function awardPoints(Request $request, Review $review)
    {
        $points = $request->input('points', \App\Models\Setting::get('review_points_amount', 50));
        $review->update(['points_given' => $points]);

        return response()->json([
            'success' => true,
            'message' => 'Points awarded successfully!',
            'points' => $points
        ]);
    }

    public function show(Review $review)
    {
        $review->load(['customer', 'booking.service']);
        return response()->json([
            'id' => $review->id,
            'user' => $review->customer->name ?? 'Anonymous',
            'service' => $review->booking->service->name ?? 'General',
            'rating' => $review->rating,
            'comment' => $review->comment ?? 'No text provided.',
            'status' => $review->status,
            'type' => $review->review_type,
            'points' => $review->points_given,
            'created' => $review->created_at->format('d M Y'),
            'avatar' => $review->customer->avatar ? asset('storage/' . $review->customer->avatar) : 'https://i.pravatar.cc/80?u=' . $review->customer_id,
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Review deleted successfully.');
    }
}
