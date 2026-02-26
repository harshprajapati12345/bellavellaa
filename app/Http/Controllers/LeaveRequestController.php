<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with(['professional', 'approver'])->orderBy('created_at', 'desc')->get();
        $pendingCount = $requests->where('status', 'Pending')->count();
        $approvedCount = $requests->where('status', 'Approved')->count();
        $rejectedCount = $requests->where('status', 'Rejected')->count();
        
        $approvedMonth = LeaveRequest::where('status', 'Approved')
            ->whereMonth('start_date', now()->month)
            ->count();
            
        $onLeaveToday = LeaveRequest::where('status', 'Approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        return view('professionals.leaves.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount', 'approvedMonth', 'onLeaveToday'));
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Leave request rejected.');
    }
}
