<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\LeaveRequest;
use App\Http\Resources\Api\LeaveRequestResource;
use App\Http\Requests\Api\Admin\UpdateLeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends BaseController
{
    /**
     * Display a listing of leave requests.
     */
    public function index(Request $request): JsonResponse
    {
        $query = LeaveRequest::with(['professional', 'approver']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return $this->success(LeaveRequestResource::collection($requests), 'Leave requests retrieved.');
    }

    /**
     * Approve or reject a leave request.
     */
    public function update(UpdateLeaveRequest $request, LeaveRequest $leave_request): JsonResponse
    {
        $leave_request->update([
            'status'      => $request->status,
            'approved_by' => Auth::id(),
        ]);

        return $this->success(new LeaveRequestResource($leave_request), "Leave request {$request->status}.");
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy(LeaveRequest $leave_request): JsonResponse
    {
        $leave_request->delete();
        return $this->success(null, 'Leave request deleted.');
    }
}
