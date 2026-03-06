<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'house_number' => $this->house_number,
            'landmark' => $this->landmark,
            'address' => $this->address, // Keep for compatibility
            'city' => $this->city,
            'pincode' => $this->zip, // Map zip to pincode for Flutter
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
