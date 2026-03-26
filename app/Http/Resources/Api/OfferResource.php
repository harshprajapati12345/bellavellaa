<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the offer into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'image'          => $this->image,
            'code'           => $this->code,
            'discount_type'  => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount_paise' => $this->max_discount_paise,
            'min_order_paise'    => $this->min_order_paise,
            'usage_limit'        => $this->usage_limit,
            'per_user_limit'     => $this->per_user_limit,
            'times_used'         => $this->times_used,
            'target_type'        => $this->target_type,
            'target_id'          => $this->target_id,
            'valid_from'     => $this->valid_from,
            'valid_until'    => $this->valid_until,
            'status'         => $this->status,
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
