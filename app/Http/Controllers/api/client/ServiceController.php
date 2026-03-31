<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\ServiceVariantResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends BaseController
{
    /**
     * GET /api/client/services/{service_id}/variants
     *
     * Returns all active variants for a specific service.
     */
    public function variants(int $serviceId): JsonResponse
    {
        $service = Service::where('status', 'Active')
            ->findOrFail($serviceId);

        $variants = $service->variants()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return $this->success(
            ServiceVariantResource::collection($variants),
            'Service variants retrieved successfully.'
        );
    }
}
