<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isPercentage = $this->discount_type === 'percentage';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'image' => $this->image,
            'type' => $isPercentage ? 'percentage' : 'flat',
            'value' => $isPercentage
                ? (int) round((float) $this->discount_value)
                : (int) round(((float) $this->discount_value) * 100),
            'max_discount_paise' => $this->max_discount_paise,
            'min_order_paise' => $this->min_order_paise,
            'usage_limit' => $this->usage_limit,
            'per_user_limit' => $this->per_user_limit,
            'times_used' => $this->times_used,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'starts_at' => $this->valid_from,
            'ends_at' => $this->valid_until,
            'is_active' => $this->status === 'Active',
        ];
    }
}