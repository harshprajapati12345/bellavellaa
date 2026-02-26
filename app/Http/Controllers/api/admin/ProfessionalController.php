<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Professional;
use App\Http\Resources\Api\ProfessionalResource;
use App\Http\Requests\Api\Admin\StoreProfessionalRequest;
use App\Http\Requests\Api\Admin\UpdateProfessionalRequest;
use Illuminate\Http\JsonResponse;

class ProfessionalController extends BaseController
{
    /**
     * Display a listing of the professionals.
     */
    public function index(): JsonResponse
    {
        $query = Professional::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($category = request('category')) {
            $query->where('category', $category);
        }

        if ($verification = request('verification')) {
            $query->where('verification', $verification);
        }

        $professionals = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'professionals' => ProfessionalResource::collection($professionals),
            'pagination' => [
                'total' => $professionals->total(),
                'count' => $professionals->count(),
                'per_page' => $professionals->perPage(),
                'current_page' => $professionals->currentPage(),
                'total_pages' => $professionals->lastPage(),
            ]
        ], 'Professionals retrieved successfully.');
    }

    /**
     * Store a newly created professional in storage.
     */
    public function store(StoreProfessionalRequest $request): JsonResponse
    {
        $professional = Professional::create(array_merge($request->validated(), [
            'joined' => now(),
        ]));

        return $this->success(new ProfessionalResource($professional), 'Professional created successfully.', 201);
    }

    /**
     * Display the specified professional.
     */
    public function show(Professional $professional): JsonResponse
    {
        return $this->success(new ProfessionalResource($professional), 'Professional retrieved successfully.');
    }

    /**
     * Update the specified professional in storage.
     */
    public function update(UpdateProfessionalRequest $request, Professional $professional): JsonResponse
    {
        $professional->update($request->validated());

        return $this->success(new ProfessionalResource($professional), 'Professional updated successfully.');
    }

    /**
     * Remove the specified professional from storage.
     */
    public function destroy(Professional $professional): JsonResponse
    {
        $professional->delete();

        return $this->success(null, 'Professional deleted successfully.');
    }

    /**
     * Approve or Reject professional verification.
     */
    public function verify(Professional $professional): JsonResponse
    {
        request()->validate([
            'status' => 'required|in:Verified,Rejected',
        ]);

        $professional->update([
            'verification' => request('status'),
        ]);

        return $this->success(new ProfessionalResource($professional), 'Verification status updated.');
    }

    /**
     * Toggle professional status (Active/Suspended/Blocked).
     */
    public function toggleStatus(Professional $professional): JsonResponse
    {
        request()->validate([
            'status' => 'required|in:Active,Suspended,Blocked',
        ]);

        $professional->update([
            'status' => request('status'),
        ]);

        return $this->success(new ProfessionalResource($professional), 'Professional status updated.');
    }
}
