<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the package into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'category'          => $this->category,
            'services'          => $this->services, // Cast as array in Model
            'price'             => $this->price,
            'discount'          => $this->discount,
            'duration'          => $this->duration,
            'description'       => $this->description,
            'desc_title'        => $this->desc_title,
            'desc_image'        => $this->desc_image,
            'aftercare_content' => $this->aftercare_content,
            'aftercare_image'   => $this->aftercare_image,
            'status'            => $this->status,
            'featured'          => (bool) $this->featured,
            'image'             => $this->image,
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
