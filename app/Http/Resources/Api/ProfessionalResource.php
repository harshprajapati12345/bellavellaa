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

            // Root-level documents for simpler UI logic
            'aadhaar_front' => MediaPathNormalizer::url($this->aadhaar_front),
            'aadhaar_back'  => MediaPathNormalizer::url($this->aadhaar_back),
            'pan_card'      => MediaPathNormalizer::url($this->pan_img),
            'pan_img'       => MediaPathNormalizer::url($this->pan_img), // Alias for consistency
            'light_bill'    => MediaPathNormalizer::url($this->light_bill),
            'bank_proof'    => MediaPathNormalizer::url($this->bank_proof),

            // Document Statuses at root level
            'aadhaar_front_status' => $this->aadhaar_status ?? 'pending',
            'aadhaar_back_status'  => $this->aadhaar_status ?? 'pending',
            'pan_card_status'      => $this->pan_status ?? 'pending',
            'light_bill_status'    => $this->light_bill_status ?? 'pending',
            'bank_proof_status'    => $this->bank_proof_status ?? 'pending',

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
            'reject_count'        => (int) ($this->reject_count ?? 0),
            'last_reject_date'    => $this->last_reject_date,
            'is_suspended'        => $this->status === 'suspended',
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
