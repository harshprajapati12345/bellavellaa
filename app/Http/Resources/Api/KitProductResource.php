<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitProductResource extends JsonResource
{
    /**
     * Transform the kit product into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'sku'             => $this->sku,
            'name'            => $this->name,
            'price'           => $this->price,
            'min_stock'       => $this->min_stock,
            'total_stock'     => $this->total_stock,
            'available_stock' => $this->available_stock,
            'status'          => $this->status,
            'is_low_stock'    => $this->available_stock <= $this->min_stock && $this->available_stock > 0,
            'is_out_of_stock' => $this->available_stock == 0,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
