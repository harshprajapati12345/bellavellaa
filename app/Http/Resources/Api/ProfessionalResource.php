<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalResource extends JsonResource
{
    /**
     * Transform the professional into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar'     => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'status'     => $this->status,
            'services'   => $this->services,
            'bio'        => $this->bio,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
