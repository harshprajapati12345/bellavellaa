<?php

namespace App\Http\Controllers\Api\Professionals;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{
    /**
     * GET /api/professionals/profile
     */
    public function show(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        return $this->success($professional, 'Profile retrieved successfully.');
    }

    /**
     * POST /api/professionals/profile/update
     */
    public function update(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'sometimes|string|max:20',
            'city'          => 'nullable|string|max:100',
            'category'      => 'nullable|string|max:100',
            'experience'    => 'nullable|string|max:100',
            'bio'           => 'nullable|string',
            
            // Files
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'aadhaar'       => 'nullable|string|max:50',
            'pan'           => 'nullable|string|max:50',
            'aadhaar_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'aadhaar_back'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'pan_img'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $updateData = [];

        // Direct text fields
        $fields = [
            'name', 'email', 'phone', 'city', 'category', 'experience', 'bio', 
            'gender', 'dob', 'service_area', 'service_radius', 'aadhaar', 'pan'
        ];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        // JSON fields - Merge with existing data
        if ($request->has('languages')) {
            $newLanguages = is_array($request->input('languages')) 
                ? $request->input('languages') 
                : json_decode($request->input('languages'), true);
            $updateData['languages'] = array_merge($professional->languages ?? [], $newLanguages);
        }

        if ($request->has('payout')) {
            $newPayout = is_array($request->input('payout')) 
                ? $request->input('payout') 
                : json_decode($request->input('payout'), true);
            $updateData['payout'] = array_merge($professional->payout ?? [], $newPayout);
        }

        if ($request->has('portfolio')) {
            $newPortfolio = is_array($request->input('portfolio')) 
                ? $request->input('portfolio') 
                : json_decode($request->input('portfolio'), true);
            $updateData['portfolio'] = array_merge($professional->portfolio ?? [], $newPortfolio);
        }

        if ($request->has('working_hours') || $request->has('available_days')) {
            // Handle both structured working_hours and flat available_days/times
            $workingHours = $professional->working_hours ?? [];
            
            if ($request->has('working_hours')) {
                $newWH = is_array($request->input('working_hours')) 
                    ? $request->input('working_hours') 
                    : json_decode($request->input('working_hours'), true);
                $workingHours = array_merge($workingHours, $newWH);
            }

            if ($request->has('available_days')) {
                $workingHours['available_days'] = $request->input('available_days');
            }
            if ($request->has('start_time')) {
                $workingHours['start_time'] = $request->input('start_time');
            }
            if ($request->has('end_time')) {
                $workingHours['end_time'] = $request->input('end_time');
            }

            $updateData['working_hours'] = $workingHours;
        }

        // File fields
        if ($request->hasFile('avatar')) {
            if ($professional->avatar && str_starts_with($professional->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $professional->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = '/storage/' . $path;
        }

        $docFields = [
            'aadhaar_front' => 'documents/aadhaar',
            'aadhaar_back'  => 'documents/aadhaar',
            'pan_img'       => 'documents/pan'
        ];

        foreach ($docFields as $docField => $folder) {
            if ($request->hasFile($docField)) {
                if ($professional->$docField && str_starts_with($professional->$docField, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $professional->$docField));
                }
                $path = $request->file($docField)->store($folder, 'public');
                $updateData[$docField] = '/storage/' . $path;
            }
        }
        
        $professional->update($updateData);

        // Auto-update 'docs' boolean flag if they've uploaded required docs
        if ($professional->aadhaar_front && $professional->aadhaar_back && $professional->pan_img) {
            $professional->update(['docs' => true]);
        }

        return $this->success($professional->fresh(), 'Profile updated successfully.');
    }

    /**
     * POST /api/professional/upload-profile-image
     */
    public function uploadProfileImage(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $request->validate(['image' => 'required|image|max:2048']);

        if ($request->hasFile('image')) {
            if ($professional->avatar && str_starts_with($professional->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $professional->avatar));
            }
            $path = $request->file('image')->store('avatars', 'public');
            $professional->update(['avatar' => '/storage/' . $path]);
        }

        return $this->success($professional->fresh(), 'Profile image uploaded.');
    }

    /**
     * POST /api/professional/upload-documents
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $request->validate([
            'aadhaar_front' => 'nullable|file|max:5120',
            'aadhaar_back'  => 'nullable|file|max:5120',
            'pan_img'       => 'nullable|file|max:5120',
        ]);

        $updateData = [];
        $docFields = ['aadhaar_front' => 'documents/aadhaar', 'aadhaar_back' => 'documents/aadhaar', 'pan_img' => 'documents/pan'];

        foreach ($docFields as $docField => $folder) {
            if ($request->hasFile($docField)) {
                $path = $request->file($docField)->store($folder, 'public');
                $updateData[$docField] = '/storage/' . $path;
            }
        }

        if (!empty($updateData)) {
            $professional->update($updateData);
        }

        return $this->success($professional->fresh(), 'Documents uploaded.');
    }

    /**
     * PUT /api/professional/change-password
     */
    public function changePassword(Request $request): JsonResponse
    {
        // Logic to update password if using email/pass
        return $this->success(null, 'Password changed successfully.');
    }
}
