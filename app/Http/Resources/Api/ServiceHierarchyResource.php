<?php

namespace App\Http\Resources\Api;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceHierarchyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->resource['item'] ?? null;
        $level = $this->resource['level'] ?? 'unknown';
        $children = collect($this->resource['children'] ?? []);

        return [
            'level' => $level,
            'item' => $item ? $this->transformNode($item) : null,
            'children_type' => $this->resource['children_type'] ?? null,
            'children' => $children->map(fn ($child) => $this->transformNode($child))->values(),
            'breadcrumbs' => collect($this->resource['breadcrumbs'] ?? [])->filter()->map(function ($crumb) {
                $level = match (true) {
                    $crumb instanceof Category => 'category',
                    $crumb instanceof ServiceGroup => 'service_group',
                    $crumb instanceof ServiceType => 'service_type',
                    $crumb instanceof Service => 'service',
                    $crumb instanceof ServiceVariant => 'variant',
                    default => 'unknown',
                };
                return [
                    'id' => $crumb->id ?? null,
                    'name' => $crumb->name ?? null,
                    'slug' => $crumb->slug ?? null,
                    'level' => $level,
                ];
            })->values(),
            'has_children' => $children->isNotEmpty(),
            'has_variants' => $this->resource['has_variants'] ?? false,
            'is_bookable' => (bool) ($this->resource['is_bookable'] ?? false),
            'bookable_type' => $this->resource['bookable_type'] ?? null,
        ];
    }

    protected function transformNode(mixed $node): array
    {
        return match (true) {
            $node instanceof Category => (new CategoryResource($node))->toArray(request()) + [
                'level' => 'category',
                'next_level' => 'service_group',
            ],
            $node instanceof ServiceGroup => (new ServiceGroupResource($node))->toArray(request()) + [
                'level' => 'service_group',
                'next_level' => 'service_type',
            ],
            $node instanceof ServiceType => (new ServiceTypeResource($node))->toArray(request()) + [
                'next_level' => 'service',
            ],
            $node instanceof Service => (new ServiceResource($node))->toArray(request()) + [
                'level' => 'service',
                'next_level' => ($node->has_variants && ($node->relationLoaded('variants') ? $node->variants->isNotEmpty() : false)) ? 'variant' : null,
                'is_bookable' => $node->canBeBookedDirectly(),
                'bookable_type' => $node->canBeBookedDirectly() ? 'service' : null,
            ],
            $node instanceof ServiceVariant => (new ServiceVariantResource($node))->toArray(request()) + [
                'level' => 'variant',
                'next_level' => null,
                'is_bookable' => $node->isBookable(),
                'bookable_type' => $node->isBookable() ? 'variant' : null,
            ],
            default => [],
        };
    }
}
