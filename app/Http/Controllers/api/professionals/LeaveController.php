<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends BaseController
{
    /**
     * GET /api/professional/leaves
     * List all leave requests for the authenticated professional
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $leaves = LeaveRequest::where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($l) => [
                'id'         => $l->id,
                'ref'        => 'LR-' . (1000 + $l->id),
                'type'       => $l->leave_type,
                'start_date' => $l->start_date,
                'end_date'   => $l->end_date,
                'total_days' => \Carbon\Carbon::parse($l->start_date)->diffInDays(\Carbon\Carbon::parse($l->end_date)) + 1,
                'reason'     => $l->reason,
                'status'     => $l->status,
                'applied_on' => $l->created_at->format('Y-m-d'),
            ]);

        return $this->success($leaves, 'Leave requests retrieved.');
    }

    /**
     * POST /api/professional/leaves
     * Apply for a leave
     */
    public function store(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'leave_type' => 'required|in:Sick Leave,Casual Leave,Emergency Leave,Personal Leave,Other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
        ]);

        $leave = LeaveRequest::create([
            'professional_id' => $professional->id,
            'leave_type'      => $validated['leave_type'],
            'start_date'      => $validated['start_date'],
            'end_date'        => $validated['end_date'],
            'reason'          => $validated['reason'],
            'status'          => 'Pending',
        ]);

        return $this->success([
            'id'         => $leave->id,
            'ref'        => 'LR-' . (1000 + $leave->id),
            'type'       => $leave->leave_type,
            'start_date' => $leave->start_date,
            'end_date'   => $leave->end_date,
            'total_days' => \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1,
            'reason'     => $leave->reason,
            'status'     => $leave->status,
        ], 'Leave request submitted successfully.', 201);
    }

    /**
     * DELETE /api/professional/leaves/{id}
     * Cancel/delete a pending leave request
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');

        $leave = LeaveRequest::where('professional_id', $professional->id)->findOrFail($id);

        if ($leave->status !== 'Pending') {
            return $this->error('Only pending leave requests can be cancelled.', 422);
        }

        $leave->delete();

        return $this->success(null, 'Leave request cancelled.');
    }
}
