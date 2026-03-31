<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'avatar'              => MediaPathNormalizer::url($this->avatar),
            'category'            => $this->category,
            'city'                => $this->city,
            'bio'                 => $this->bio,
            'experience'          => $this->experience,
            'rating'              => $this->rating,
            'verification_status' => $this->verification ?? 'Not Started',
            'status'              => $this->status,
            'services'            => $this->services,
            'is_online'           => (bool) $this->is_online,
            'documents'           => [
                'aadhaar_front' => [
                    'url' => MediaPathNormalizer::url($this->aadhaar_front),
                    'status' => $this->aadhaar_status ?? 'pending',
                ],
                'aadhaar_back' => [
                    'url' => MediaPathNormalizer::url($this->aadhaar_back),
                    'status' => $this->aadhaar_status ?? 'pending',
                ],
                'pan_card' => [
                    'url' => MediaPathNormalizer::url($this->pan_img),
                    'status' => $this->pan_status ?? 'pending',
                ],
                'light_bill' => [
                    'url' => MediaPathNormalizer::url($this->light_bill),
                    'status' => $this->light_bill_status ?? 'pending',
                ],
                'bank_proof' => [
                    'url' => MediaPathNormalizer::url($this->bank_proof),
                    'status' => $this->bank_proof_status ?? 'pending',
                ],
                'upi' => [
                    'upi_id' => $this->upi_id,
                    'status' => $this->upi_status ?? 'pending',
                ],
            ],
            'last_seen'           => $this->last_seen?->toIso8601String(),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
