<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Resources\Api\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $addresses = $this->guard()->user()->addresses()->latest()->get();
        return $this->success(AddressResource::collection($addresses), 'Addresses retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string',
            'zip' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $this->guard()->user()->addresses()->update(['is_default' => false]);
        }

        $address = $this->guard()->user()->addresses()->create($validated);

        return $this->success(new AddressResource($address), 'Address created successfully.', 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        if ($address->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $validated = $request->validate([
            'label' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'city' => 'nullable|string',
            'zip' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $this->guard()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return $this->success(new AddressResource($address), 'Address updated successfully.');
    }

    public function destroy(Address $address): JsonResponse
    {
        if ($address->customer_id !== $this->guard()->id()) {
            return $this->error('Unauthorized.', 403);
        }

        $address->delete();

        return $this->success(null, 'Address deleted successfully.');
    }
}
