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
            'packages' => $packages
                ->map(fn (Package $package) => $this->packageSummaryPayload($package, $context))
                ->values(),
        ], 'Packages retrieved successfully.');
    }

    public function featured(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $limit = (int) ($validated['limit'] ?? 8);

        $packages = Package::query()
            ->active()
            ->where('featured', true)
            ->with([
                'contexts',
                'groups.items.options',
                'groups.items.service.activeVariants',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($packages->isEmpty()) {
            $packages = Package::query()
                ->active()
                ->with([
                    'contexts',
                    'groups.items.options',
                    'groups.items.service.activeVariants',
                ])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit($limit)
                ->get();
        }

        return $this->success([
            'packages' => $packages
                ->map(fn (Package $package) => $this->packageSummaryPayload($package))
                ->filter()
                ->values(),
        ], 'Featured packages retrieved successfully.');
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

    protected function packageSummaryPayload(Package $package, ?array $context = null): ?array
    {
        $resolvedContext = $context ?? $this->primaryContextPayload($package);
        if ($resolvedContext === null) {
            return null;
        }

        return (new PackageSummaryResource($package))->toArray(request()) + [
            'context' => [
                'type' => $resolvedContext['type'],
                'id' => $resolvedContext['id'],
                'name' => $resolvedContext['name'],
                'slug' => $resolvedContext['slug'],
            ],
        ];
    }

    protected function primaryContextPayload(Package $package): ?array
    {
        $package->loadMissing('contexts');
        $record = $package->contexts->first();
        if ($record === null) {
            return null;
        }

        return $this->packageService->assertPackageContext(
            $package,
            $record->context_type,
            (int) $record->context_id
        );
    }
}
