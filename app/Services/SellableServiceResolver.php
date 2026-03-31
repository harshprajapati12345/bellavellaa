<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Validation\ValidationException;

class SellableServiceResolver
{
    public function resolveForService(Service $service, ?ServiceVariant $variant = null): array
    {
        if ($service->status !== 'Active') {
            throw ValidationException::withMessages([
                'service_id' => 'The selected service is inactive.',
            ]);
        }

        if ($variant !== null) {
            if ((int) $variant->service_id !== (int) $service->id) {
                throw ValidationException::withMessages([
                    'service_variant_id' => 'The selected variant does not belong to the selected service.',
                ]);
            }

            if (!$variant->isBookable()) {
                throw ValidationException::withMessages([
                    'service_variant_id' => 'The selected variant is not bookable.',
                ]);
            }

            return [
                'bookable_type' => 'variant',
                'sellable_type' => 'variant',
                'sellable_id' => $variant->id,
                'service' => $service,
                'variant' => $variant,
                'name' => $variant->name,
                'display_price' => $variant->display_price,
                'original_price' => $variant->original_price,
                'sale_price' => $variant->sale_price,
                'is_discounted' => $variant->is_discounted,
                'duration_minutes' => $variant->resolved_duration_minutes,
            ];
        }

        if (!$service->canBeBookedDirectly()) {
            throw ValidationException::withMessages([
                'service_id' => 'This service requires a variant selection before booking.',
            ]);
        }

        return [
            'bookable_type' => 'service',
            'sellable_type' => 'service',
            'sellable_id' => $service->id,
            'service' => $service,
            'variant' => null,
            'name' => $service->name,
            'display_price' => $service->display_price,
            'original_price' => $service->original_price,
            'sale_price' => $service->sale_price,
            'is_discounted' => $service->is_discounted,
            'duration_minutes' => $service->resolved_duration_minutes,
        ];
    }
}
