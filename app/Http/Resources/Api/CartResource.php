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
        $unitPrice = (float) $this->resolved_unit_price;
        $image = data_get($this->meta, 'package_snapshot.image') ?? $item->image ?? null;
        $duration = $this->resolved_duration_minutes;
        $displayName = $this->resolved_display_name;

        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'service_id' => $this->service_id,
            'service_variant_id' => $this->service_variant_id,
            'package_id' => $this->package_id,
            'name' => $displayName,
            'display_name' => $displayName,
            'service_name' => $this->service?->name,
            'variant_name' => $this->variant?->name,
            'image' => MediaPathNormalizer::url($image),
            'quantity' => $this->quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $this->quantity * $unitPrice,
            'duration_minutes' => $duration,
            'is_discounted' => (bool) ($item->is_discounted ?? false),
            'sale_price' => $item->sale_price ?? null,
            'original_price' => $item->original_price ?? $unitPrice,
            'meta' => $this->meta,
            'package_context_type' => data_get($this->meta, 'context.type'),
            'package_context_id' => data_get($this->meta, 'context.id'),
            'package_context_name' => data_get($this->meta, 'context.name'),
            'package_context_slug' => data_get($this->meta, 'context.slug'),
            'package_snapshot' => data_get($this->meta, 'package_snapshot'),
            'package_configuration' => data_get($this->meta, 'configuration'),
        ];
    }
}
