<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\ServiceGroupResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\ServiceGroup;
use Illuminate\Routing\Controller;

class ClientServiceGroupController extends Controller
{
    public function show($id)
    {
        $group = ServiceGroup::where('status', 'Active')
            ->with([
                'category',
                'serviceTypes' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
                'directServices' => fn ($query) => $query->where('status', 'Active')->orderBy('sort_order'),
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ServiceGroupResource($group),
        ]);
    }

    public function services($categorySlug, $groupSlug)
    {
        $group = ServiceGroup::where('slug', $groupSlug)
            ->whereHas('category', fn ($query) => $query->where('slug', $categorySlug)->where('status', 'Active'))
            ->where('status', 'Active')
            ->firstOrFail();

        $services = $group->serviceTypes()->exists()
            ? $group->services()->where('status', 'Active')->with('serviceType')->orderBy('sort_order')->get()
            : $group->directServices()->where('status', 'Active')->with('serviceType')->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
        ]);
    }
}
