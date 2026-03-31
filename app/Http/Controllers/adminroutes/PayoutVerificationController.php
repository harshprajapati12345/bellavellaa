<?php

namespace App\Http\Controllers\adminroutes;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = VerificationRequest::with('professional');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('professional', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('professionals.payout_verifications.index', compact('requests', 'status', 'search'));
    }

    public function approve($id)
    {
        $req = VerificationRequest::findOrFail($id);

        DB::transaction(function () use ($req) {
            $req->update(['status' => 'approved']);

            $req->professional->update([
                'payout_verification_status' => 'Verified'
            ]);
        });

        return back()->with('success', 'Payout method verified successfully.');
    }

    public function reject(Request $request, $id)
    {
        $req = VerificationRequest::findOrFail($id);
        $request->validate(['reason' => 'required|string|max:500']);

        DB::transaction(function () use ($req, $request) {
            $req->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);

            $req->professional->update([
                'payout_verification_status' => 'Rejected'
            ]);
        });

        return back()->with('error', 'Payout method verification rejected.');
    }
}
