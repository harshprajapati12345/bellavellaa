<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Api\PackageConfigResource;
use App\Http\Resources\Api\PackageSummaryResource;
use App\Models\Package;
use App\Services\ConfigurablePackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    public function __construct(
        protected ConfigurablePackageService $packageService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'context_type' => 'required|string',
            'context_id' => 'required|integer',
        ]);

        $context = $this->packageService->resolveContext(
            $validated['context_type'],
            (int) $validated['context_id']
        );

        $packages = Package::query()
            ->active()
            ->with([
                'contexts',
                'groups.items.options',
                'groups.items.service.activeVariants',
            ])
            ->whereHas('contexts', function ($query) use ($context) {
                $query->where('context_type', $context['type'])
                    ->where('context_id', $context['id']);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->success([
            'context' => [
                'type' => $context['type'],
                'id' => $context['id'],
                'name' => $context['name'],
                'slug' => $context['slug'],
            ],
            'packages' => PackageSummaryResource::collection($packages),
        ], 'Packages retrieved successfully.');
    }

    public function config(Request $request, Package $package): JsonResponse
    {
        $validated = $request->validate([
            'context_type' => 'nullable|string',
            'context_id' => 'nullable|integer',
        ]);

        $package->load([
            'contexts',
            'groups.items.options' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'groups.items.service.activeVariants' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
        ]);

        $context = $this->packageService->assertPackageContext(
            $package,
            $validated['context_type'] ?? null,
            isset($validated['context_id']) ? (int) $validated['context_id'] : null,
        );

        $resolved = $this->packageService->buildResolvedConfiguration($package);

        return $this->success([
            'package' => new PackageConfigResource([
                'package' => $package,
                'context' => $context,
                'resolved' => $resolved,
            ]),
        ], 'Package configuration retrieved successfully.');
    }
}
