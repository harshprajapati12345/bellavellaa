<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\UserReview;
use Illuminate\Http\Request;

class UserReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $reviews = UserReview::with([
                'booking.customer',
                'booking.professional',
            ])
            ->latest()
            ->get()
            ->filter(function ($review) use ($status) {
                return $status === 'all' || strcasecmp($review->status, $status) === 0;
            })
            ->values();

        return view('user-reviews.index', compact('reviews', 'status'));
    }

    public function approve(UserReview $userReview)
    {
        $userReview->update(['status' => 'Approved']);
        return back()->with('success', 'User review approved successfully.');
    }

    public function reject(UserReview $userReview)
    {
        $userReview->update(['status' => 'Rejected']);
        return back()->with('success', 'User review rejected successfully.');
    }
}
