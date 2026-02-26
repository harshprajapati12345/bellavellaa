<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'booking.service'])->orderBy('created_at', 'desc')->get();
        $stats = [
            'total'   => $reviews->count(),
            'video'   => $reviews->where('review_type', 'video')->count(),
            'pending' => $reviews->where('status', 'Pending')->count(),
            'points'  => $reviews->sum('points_given'),
        ];

        return view('reviews.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'Approved']);
        return back()->with('success', 'Review approved successfully!');
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
        $request->validate(['points' => 'required|integer|min:0']);
        $review->update(['points_given' => $request->points]);
        return back()->with('success', 'Points awarded successfully!');
    }

    public function show(Review $review)
    {
        $review->load(['user', 'booking.service']);
        return response()->json([
            'id' => $review->id,
            'user' => $review->user->name ?? 'Anonymous',
            'service' => $review->booking->service->name ?? 'General',
            'rating' => $review->rating,
            'review_text' => $review->review_text ?? 'No text provided.',
            'status' => $review->status,
            'type' => $review->review_type,
            'points' => $review->points_given,
            'created' => $review->created_at->format('d M Y'),
            'avatar' => $review->user->avatar ? asset('storage/'.$review->user->avatar) : 'https://i.pravatar.cc/80?u='.$review->user_id,
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Review deleted successfully.');
    }
}
