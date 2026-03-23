<?php

namespace App\Http\Controllers\Api\Professionals;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\VerificationRequest;
use App\Models\Professional;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\ProfessionalResource;

class ProfileController extends BaseController
{
    protected function resolveProfessional(Request $request): ?Professional
    {
        return $request->user('professional-api');
    }

    /**
     * GET /api/professionals/profile
     */
    public function show(Request $request): JsonResponse
    {
        $professional = $this->resolveProfessional($request);
        \Illuminate\Support\Facades\Log::info("Profile Fetch for Pro ID: " . ($professional ? $professional->id : 'NULL'));

        if (!$professional) {
            return $this->error('Unauthenticated.', 401);
        }

        return $this->success(new ProfessionalResource($professional), 'Profile retrieved successfully.');
    }

    /**
     * POST /api/professionals/profile/update
     */
    public function update(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        \Illuminate\Support\Facades\Log::info("Generic Profile Update for Pro ID: {$professional->id}, has payout: " . ($request->has('payout') ? 'YES' : 'NO'));

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'city' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:100',
            'bio' => 'nullable|string',

            // Files
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'aadhaar' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'aadhaar_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'aadhaar_back' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'pan_img' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
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
            'aadhaar_back' => 'documents/aadhaar',
            'pan_img' => 'documents/pan'
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

        return $this->success(new ProfessionalResource($professional->fresh()), 'Profile updated successfully.');
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

        return $this->success(new ProfessionalResource($professional->fresh()), 'Profile image uploaded.');
    }

    /**
     * POST /api/professional/upload-documents
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        if ($professional->verification === 'Verified') {
            return $this->error('Documents cannot be modified after verification.', 403);
        }

        $request->validate([
            'aadhaar_front' => 'nullable|file|max:5120',
            'aadhaar_back' => 'nullable|file|max:5120',
            'pan_img' => 'nullable|file|max:5120',
            'certificate_img' => 'nullable|file|max:5120',
            'selfie' => 'nullable|file|max:5120',
        ]);

        $updateData = [];
        $docFields = [
            'aadhaar_front' => 'documents/aadhaar',
            'aadhaar_back' => 'documents/aadhaar',
            'pan_img' => 'documents/pan',
            'certificate_img' => 'documents/certificate',
            'selfie' => 'documents/selfies'
        ];

        foreach ($docFields as $docField => $folder) {
            if ($request->hasFile($docField)) {
                $path = $request->file($docField)->store($folder, 'public');
                $updateData[$docField] = '/storage/' . $path;
            }
        }

        if (!empty($updateData)) {
            $professional->update($updateData);
        }

        return $this->success(new ProfessionalResource($professional->fresh()), 'Documents uploaded.');
    }

    public function updateBankDetails(Request $request): JsonResponse
    {
        $professional = $this->resolveProfessional($request);
        \Illuminate\Support\Facades\Log::info("DEBUG: updateBankDetails hit for Pro ID: " . ($professional ? $professional->id : 'NULL'));

        if (!$professional) {
            return $this->error('Unauthenticated.', 401);
        }

        /*
         if ($professional->payout_verification_status === 'Verified') {
         return $this->error('Bank details cannot be modified after verification.', 403);
         }
         */

        $request->validate([
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'bank_proof_image' => 'nullable|file|max:5120',
        ]);

        $payout = is_array($professional->payout) ? $professional->payout : [];
        $payout['account_holder'] = $request->account_holder;
        $payout['bank_name'] = $request->bank_name;
        $payout['account_number'] = $request->account_number;
        $payout['ifsc'] = $request->ifsc;
        $payout['branch'] = $request->branch;

        $updateData = [
            'payout' => $payout,
            'payout_verification_status' => 'Pending',
        ];

        if ($request->hasFile('bank_proof_image')) {
            if ($professional->bank_proof_image && str_starts_with($professional->bank_proof_image, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $professional->bank_proof_image));
            }
            $path = $request->file('bank_proof_image')->store('documents/payout', 'public');
            $updateData['bank_proof_image'] = '/storage/' . $path;
        }

        $professional->update($updateData);

        \Illuminate\Support\Facades\Log::info("Creating VerificationRequest for Pro ID: {$professional->id}, Type: bank");

        VerificationRequest::updateOrCreate(
        ['professional_id' => $professional->id, 'type' => 'bank'],
        ['status' => 'pending', 'rejection_reason' => null]
        );

        return $this->success(new ProfessionalResource($professional->fresh()), 'Bank details updated successfully.');
    }

    public function updateUPIDetails(Request $request): JsonResponse
    {
        $professional = $this->resolveProfessional($request);
        \Illuminate\Support\Facades\Log::info("DEBUG: updateUPIDetails hit for Pro ID: " . ($professional ? $professional->id : 'NULL'));

        if (!$professional) {
            return $this->error('Unauthenticated.', 401);
        }

        /*
         if ($professional->payout_verification_status === 'Verified') {
         return $this->error('UPI details cannot be modified after verification.', 403);
         }
         */

        $request->validate([
            'upi_id' => 'required|string|max:255',
            'upi_screenshot' => 'nullable|file|max:5120',
        ]);

        $payout = is_array($professional->payout) ? $professional->payout : [];
        $payout['upi_id'] = $request->upi_id;

        $updateData = [
            'payout' => $payout,
            'payout_verification_status' => 'Pending',
        ];

        if ($request->hasFile('upi_screenshot')) {
            if ($professional->upi_screenshot && str_starts_with($professional->upi_screenshot, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $professional->upi_screenshot));
            }
            $path = $request->file('upi_screenshot')->store('documents/payout', 'public');
            $updateData['upi_screenshot'] = '/storage/' . $path;
        }

        $professional->update($updateData);

        \Illuminate\Support\Facades\Log::info("Creating VerificationRequest for Pro ID: {$professional->id}, Type: upi");

        VerificationRequest::updateOrCreate(
        ['professional_id' => $professional->id, 'type' => 'upi'],
        ['status' => 'pending', 'rejection_reason' => null]
        );

        return $this->success(new ProfessionalResource($professional->fresh()), 'UPI details updated successfully.');
    }

    /**
     * POST /api/professional/update-fcm-token
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);

        $professional = $request->user('professional-api');
        $professional->update(['fcm_token' => $request->fcm_token]);

        return $this->success(null, 'FCM token updated successfully.');
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
