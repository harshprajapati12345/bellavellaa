<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Resources\Api\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function show(): JsonResponse
    {
        return $this->success(new CustomerResource($this->guard()->user()), 'Profile retrieved successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $customer = $this->guard()->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:customers,email,' . $customer->id,
            'date_of_birth' => 'sometimes|date|before:today',
            'avatar_file' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar_file')) {
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }
            $validated['avatar'] = $request->file('avatar_file')->store('avatars', 'public');
        }

        $customer->update($validated);

        return $this->success(new CustomerResource($customer), 'Profile updated successfully.');
    }
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $this->guard()->user()->update([
            'fcm_token' => $request->token
        ]);

        return $this->success(null, 'FCM token updated successfully.');
    }
}

