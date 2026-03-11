<?php

namespace App\Http\Resources\Api;

use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->sellable_item;
        $unitPrice = (float) ($item->display_price ?? $item->price ?? 0);
        $image = $item->image ?? null;

        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'service_id' => $this->service_id,
            'service_variant_id' => $this->service_variant_id,
            'name' => $item->name ?? 'Unknown',
            'display_name' => $item->name ?? 'Unknown',
            'service_name' => $this->service?->name,
            'variant_name' => $this->variant?->name,
            'image' => MediaPathNormalizer::url($image),
            'quantity' => $this->quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $this->quantity * $unitPrice,
            'duration_minutes' => $item->resolved_duration_minutes ?? $item->duration_minutes ?? $item->duration ?? null,
            'is_discounted' => (bool) ($item->is_discounted ?? false),
            'sale_price' => $item->sale_price ?? null,
            'original_price' => $item->original_price ?? $unitPrice,
        ];
    }
}
