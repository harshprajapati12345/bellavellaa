<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Professional;
use App\Http\Resources\Api\ProfessionalResource;
use App\Http\Requests\Api\Admin\StoreProfessionalRequest;
use App\Http\Requests\Api\Admin\UpdateProfessionalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalController extends BaseController
{
    /**
     * Display a listing of professionals.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Professional::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $professionals = $query->latest()->orderBy('id', 'desc')->paginate($request->input('per_page', 15));

        return $this->success(ProfessionalResource::collection($professionals), 'Professionals retrieved.');
    }

    /**
     * Store a newly created professional.
     */
    public function store(StoreProfessionalRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('professionals', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $professional = Professional::create($data);

        return $this->success(new ProfessionalResource($professional), 'Professional created successfully.', 201);
    }

    /**
     * Display the specified professional.
     */
    public function show(Professional $professional): JsonResponse
    {
        return $this->success(new ProfessionalResource($professional), 'Professional details retrieved.');
    }

    /**
     * Update the specified professional.
     */
    public function update(UpdateProfessionalRequest $request, Professional $professional): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('professionals', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $professional->update($data);

        return $this->success(new ProfessionalResource($professional), 'Professional updated successfully.');
    }

    /**
     * Remove the specified professional.
     */
    public function destroy(Professional $professional): JsonResponse
    {
        $professional->delete();
        return $this->success(null, 'Professional deleted successfully.');
    }

    /**
     * Suspend a professional.
     */
    public function suspend(Request $request, $id): JsonResponse
    {
        $professional = Professional::findOrFail($id);
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($professional) {
            $professional->update([
                'status' => 'suspended',
                'is_online' => false,
            ]);


            // Atomic Booking Handover
            \App\Models\Booking::where('professional_id', $professional->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->update([
                    'status' => 'cancelled',
                    'cancel_reason' => 'professional_suspended'
                ]);
        });

        return $this->success(new ProfessionalResource($professional->fresh()), 'Professional suspended and active bookings cancelled.');
    }


    /**
     * Reactivate a suspended professional.
     */
    public function unsuspend(Request $request, $id): JsonResponse
    {
        $professional = Professional::findOrFail($id);
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($professional) {
            $professional->update([
                'status' => 'active',
                'reject_count' => 0,
                'last_reset_date' => now()->toDateString(),
            ]);

            // 🔥 Real-time Broadcast for instant Flutter refresh
            $professional->refresh();
            broadcast(new \App\Events\ProfessionalStatusUpdated($professional))->toOthers();
        });


        return $this->success(new ProfessionalResource($professional->fresh()), 'Professional reactivated successfully.');
    }

}
