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
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:10',
            'address' => 'nullable|string',
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
}
