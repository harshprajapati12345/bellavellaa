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
        $fields = ['name', 'email', 'phone', 'city', 'category', 'experience', 'bio', 'aadhaar', 'pan'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
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
}
