<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitOrderResource extends JsonResource
{
    /**
     * Transform the kit order into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'professional'  => new ProfessionalResource($this->whenLoaded('professional')),
            'product'       => new KitProductResource($this->whenLoaded('kitProduct')),
            'quantity'      => $this->quantity,
            'total_amount'  => (float) ($this->total_amount / 100),
            'payment_method'=> $this->payment_method,
            'status'        => $this->status,
            'assigned_at'   => $this->assigned_at?->toIso8601String(),
            'received_at'   => $this->received_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
