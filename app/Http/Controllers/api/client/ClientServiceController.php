<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\ServiceResource;
use App\Http\Resources\Api\ServiceVariantResource;
use App\Models\Service;
use Illuminate\Routing\Controller;

class ClientServiceController extends Controller
{
    public function show($id)
    {
        $service = Service::with([
            'serviceType.serviceGroup.category',
            'includedItems' => fn ($query) => $query->orderBy('sort_order'),
            'addons' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            'variants' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
        ])->where('status', 'Active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully',
            'data' => new ServiceResource($service),
        ], 200);
    }

    public function variants($serviceId)
    {
        $service = Service::where('status', 'Active')->findOrFail($serviceId);
        $variants = $service->variants()->where('status', 'Active')->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => ServiceVariantResource::collection($variants),
        ]);
    }
}
