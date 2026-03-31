<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Resources\Api\ServiceHierarchyResource;
use App\Services\ServiceHierarchyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientServiceHierarchyController extends BaseController
{
    public function __construct(
        protected ServiceHierarchyService $hierarchyService
    ) {
    }

    public function show(Request $request, string $nodeKey): JsonResponse
    {
        $payload = $this->hierarchyService->resolveNode(
            $nodeKey,
            $request->query('level')
        );

        return $this->success(
            new ServiceHierarchyResource($payload),
            'Hierarchy node retrieved successfully.'
        );
    }
}
