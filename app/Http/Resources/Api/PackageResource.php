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
            'slug'              => $this->slug,
            'category_id'       => $this->category_id,
            // Services via package_service pivot — loaded with ->with('services')
            'services'          => $this->whenLoaded('services', fn() =>
                $this->services->map(fn($s) => [
                    'id'    => $s->id,
                    'name'  => $s->name,
                    'price' => $s->price,
                ])
            ),
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
            'sort_order'        => $this->sort_order,
            'image'             => $this->image,
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
