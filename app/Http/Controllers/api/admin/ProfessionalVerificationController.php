<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Professional;
use App\Http\Resources\Api\ProfessionalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalVerificationController extends BaseController
{
    /**
     * List professionals pending verification.
     */
    public function index(): JsonResponse
    {
        $requests = Professional::where('verification', '!=', 'Verified')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(ProfessionalResource::collection($requests), 'Verification requests retrieved.');
    }

    /**
     * Approve or reject a verification request.
     */
    public function verify(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:Verified,Rejected',
            'remarks' => 'nullable|string'
        ]);

        $professional = Professional::findOrFail($id);
        
        $professional->update([
            'verification' => $request->status,
            // 'verification_remarks' => $request->remarks, // Assuming column exists or adding it later
        ]);

        return $this->success(new ProfessionalResource($professional), "Professional status updated to {$request->status}.");
    }
}
