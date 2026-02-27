<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->item;

        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'name' => $item->name ?? 'Unknown',
            'image' => $this->item_type === 'service' ? $item->image : ($item->image ?? null),
            'quantity' => $this->quantity,
            'unit_price' => (int) ($item->price ?? 0),
            'subtotal' => (int) ($this->quantity * ($item->price ?? 0)),
        ];
    }
}
