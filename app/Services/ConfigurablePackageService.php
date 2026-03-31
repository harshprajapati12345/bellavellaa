<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Package;
use App\Models\PackageContext;
use App\Models\PackageGroup;
use App\Models\PackageItem;
use App\Models\PackageItemOption;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ConfigurablePackageService
{
    public function resolveContext(?string $contextType, ?int $contextId): ?array
    {
        if (!$contextType || !$contextId) {
            return null;
        }

        $normalizedType = $this->normalizeContextType($contextType);
        $model = match ($normalizedType) {
            'category' => Category::find($contextId),
            'service_group' => ServiceGroup::find($contextId),
            default => null,
        };

        if (!$model) {
            throw ValidationException::withMessages([
                'context_id' => ['Requested package context was not found.'],
            ]);
        }

        return [
            'type' => $normalizedType,
            'id' => (int) $model->id,
            'name' => $model->name,
            'slug' => $model->slug,
            'model' => $model,
        ];
    }

    public function assertPackageContext(Package $package, ?string $contextType, ?int $contextId): ?array
    {
        if (!$contextType || !$contextId) {
            return $package->contexts->first()
                ? $this->contextPayloadFromRecord($package->contexts->first())
                : null;
        }

        $normalizedType = $this->normalizeContextType($contextType);
        $context = $package->contexts
            ->first(fn (PackageContext $record) => $record->context_type === $normalizedType && (int) $record->context_id === (int) $contextId);

        if (!$context) {
            throw ValidationException::withMessages([
                'package_id' => ['Package is not assigned to the requested context.'],
            ]);
        }

        return $this->contextPayloadFromRecord($context);
    }

    public function buildResolvedConfiguration(Package $package, ?array $submittedConfiguration = null): array
    {
        $package->loadMissing([
            'groups.items.options' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'groups.items.service.activeVariants' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
        ]);

        $selectionMap = $this->selectionMap($submittedConfiguration);

        $groupsPayload = [];
        $selectedTotal = 0.0;
        $durationMinutes = 0;

        foreach ($package->groups as $group) {
            $itemsPayload = [];

            foreach ($group->items as $item) {
                $resolvedItem = $item->source_type === 'linked' && $item->service_id
                    ? $this->resolveLinkedItemSelection($item, $selectionMap[$item->id] ?? null)
                    : $this->resolveCustomItemSelection($item, $selectionMap[$item->id] ?? null);

                $itemsPayload[] = $resolvedItem;

                if ($resolvedItem['selected']) {
                    $selectedTotal += (float) $resolvedItem['selected_price'];
                    $durationMinutes += (int) $resolvedItem['selected_duration_minutes'];
                }
            }

            $groupsPayload[] = [
                'id' => $group->id,
                'source_type' => $group->source_type ?: 'custom',
                'linked_type' => $group->linked_type,
                'linked_id' => $group->linked_id,
                'title' => $this->resolveGroupTitle($group),
                'subtitle' => $group->subtitle,
                'sort_order' => $group->sort_order,
                'items' => $itemsPayload,
            ];
        }

        if ($selectedTotal <= 0) {
            $selectedTotal = (float) ($package->price ?? 0);
        }

        if ($durationMinutes <= 0) {
            $durationMinutes = (int) ($package->duration ?? 0);
        }

        $pricing = $this->resolvePackagePricing($package, $selectedTotal);

        return [
            'package_mode' => $package->package_mode ?: 'hierarchy',
            'pricing_rule' => 'threshold_discount_over_selected_total',
            'duration_rule' => 'sum_selected_options',
            'base_price_threshold' => $pricing['base_price_threshold'],
            'discount_type' => $pricing['discount_type'],
            'discount_value' => $pricing['discount_value'],
            'groups' => $groupsPayload,
            'totals' => [
                'selected_total' => round($selectedTotal, 2),
                'original_total' => round($selectedTotal, 2),
                'discount_applied' => $pricing['discount_applied'],
                'discount_percentage' => $pricing['discount_percentage'],
                'discount_amount' => round($pricing['discount_amount'], 2),
                'discounted_total' => round($pricing['final_total'], 2),
                'final_total' => round($pricing['final_total'], 2),
                'duration_minutes' => $durationMinutes,
            ],
            'preview_items' => $this->previewItemsFromGroups(collect($groupsPayload)),
        ];
    }

    public function buildCartMeta(
        Package $package,
        ?array $contextPayload,
        array $resolvedConfiguration
    ): array {
        return [
            'config_hash' => sha1(json_encode([
                'package_id' => $package->id,
                'package_mode' => $resolvedConfiguration['package_mode'] ?? $package->package_mode,
                'context_type' => $contextPayload['type'] ?? null,
                'context_id' => $contextPayload['id'] ?? null,
                'groups' => $resolvedConfiguration['groups'],
            ])),
            'context' => $contextPayload ? [
                'type' => $contextPayload['type'],
                'id' => $contextPayload['id'],
                'name' => $contextPayload['name'],
                'slug' => $contextPayload['slug'],
            ] : null,
            'package_snapshot' => [
                'id' => $package->id,
                'title' => $package->name,
                'slug' => $package->slug,
                'image' => $package->image,
                'tag_label' => $package->tag_label,
                'short_description' => $package->short_description ?: $package->description,
                'package_mode' => $resolvedConfiguration['package_mode'] ?? $package->package_mode,
                'display_price' => $resolvedConfiguration['totals']['final_total'],
                'original_price' => $resolvedConfiguration['totals']['original_total'],
                'discounted_price' => $resolvedConfiguration['totals']['discounted_total'],
                'discount_percentage' => $resolvedConfiguration['totals']['discount_percentage'],
                'duration_minutes' => $resolvedConfiguration['totals']['duration_minutes'],
                'preview_items' => $resolvedConfiguration['preview_items'],
                'is_configurable' => (bool) $package->is_configurable,
                'quantity_allowed' => (bool) $package->quantity_allowed,
                'base_price_threshold' => $resolvedConfiguration['base_price_threshold'] ?? $package->base_price_threshold,
                'discount_type' => $resolvedConfiguration['discount_type'] ?? $package->discount_type,
                'discount_value' => $resolvedConfiguration['discount_value'] ?? $package->discount_value,
            ],
            'configuration' => [
                'package_mode' => $resolvedConfiguration['package_mode'] ?? $package->package_mode,
                'pricing_rule' => $resolvedConfiguration['pricing_rule'],
                'duration_rule' => $resolvedConfiguration['duration_rule'],
                'groups' => $resolvedConfiguration['groups'],
            ],
            'totals' => $resolvedConfiguration['totals'],
        ];
    }

    public function groupCandidatesForContext(string $contextType, int $contextId): Collection
    {
        $contextType = $this->normalizeContextType($contextType);

        return match ($contextType) {
            'service_group' => ServiceType::query()
                ->where('service_group_id', $contextId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ServiceType $type) => [
                    'id' => $type->id,
                    'type' => 'service_type',
                    'title' => $type->name,
                    'subtitle' => $type->serviceGroup?->name,
                ]),
            'category' => ServiceGroup::query()
                ->where('category_id', $contextId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ServiceGroup $group) => [
                    'id' => $group->id,
                    'type' => 'service_group',
                    'title' => $group->name,
                    'subtitle' => $group->category?->name,
                ]),
            default => collect(),
        };
    }

    public function itemCandidatesForLinkedGroup(string $groupType, int $groupId): Collection
    {
        $groupType = $this->normalizeContextType($groupType);

        return match ($groupType) {
            'service_type' => Service::query()
                ->where('service_type_id', $groupId)
                ->where('status', 'Active')
                ->with('activeVariants')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (Service $service) => $this->serviceCandidatePayload($service)),
            'service_group' => Service::query()
                ->where(function ($query) use ($groupId) {
                    $query->where('service_group_id', $groupId)
                        ->orWhereHas('serviceType', fn ($serviceTypeQuery) => $serviceTypeQuery->where('service_group_id', $groupId));
                })
                ->where('status', 'Active')
                ->with('activeVariants')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->unique('id')
                ->values()
                ->map(fn (Service $service) => $this->serviceCandidatePayload($service)),
            default => collect(),
        };
    }

    protected function normalizeContextType(string $contextType): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim($contextType)));
    }

    protected function contextPayloadFromRecord(PackageContext $context): ?array
    {
        $model = $context->resolveContextModel();
        if (!$model) {
            return null;
        }

        return [
            'type' => $context->context_type,
            'id' => (int) $context->context_id,
            'name' => $model->name,
            'slug' => $model->slug,
            'model' => $model,
        ];
    }

    protected function selectionMap(?array $submittedConfiguration): array
    {
        $map = [];

        foreach (($submittedConfiguration['groups'] ?? []) as $group) {
            foreach (($group['items'] ?? []) as $item) {
                if (empty($item['item_id'])) {
                    continue;
                }

                $map[(int) $item['item_id']] = [
                    'selected' => filter_var($item['selected'] ?? true, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
                    'option_id' => isset($item['option_id']) ? (int) $item['option_id'] : null,
                    'variant_id' => isset($item['variant_id']) ? (int) $item['variant_id'] : (isset($item['selected_variant_id']) ? (int) $item['selected_variant_id'] : null),
                ];
            }
        }

        return $map;
    }

    protected function resolveGroupTitle(PackageGroup $group): string
    {
        if ($group->source_type === 'linked') {
            $linked = $group->linkedNode();
            if ($linked?->name) {
                return $linked->name;
            }
        }

        return $group->title ?: 'Package Group';
    }

    protected function resolveLinkedItemSelection(PackageItem $item, ?array $submittedItem): array
    {
        $service = $item->service;
        if (!$service) {
            throw ValidationException::withMessages([
                'configuration' => ["Linked package item [{$item->id}] is missing its service reference."],
            ]);
        }

        $variants = $service->relationLoaded('activeVariants')
            ? $service->activeVariants
            : $service->activeVariants()->orderBy('sort_order')->orderBy('id')->get();
        $variants = $variants->values();

        $selected = $submittedItem['selected'] ?? $item->is_default_selected;
        if ($item->is_required) {
            $selected = true;
        }

        $selectedVariant = null;
        if ($variants->isNotEmpty()) {
            $requestedVariantId = $submittedItem['variant_id'] ?? $submittedItem['option_id'] ?? null;
            if ($requestedVariantId) {
                $selectedVariant = $variants->firstWhere('id', $requestedVariantId);
            }

            $selectedVariant ??= $variants->firstWhere('is_default', true);
            $selectedVariant ??= ($selected ? $variants->first() : null);
        }

        if ($selected && $variants->isNotEmpty() && !$selectedVariant) {
            throw ValidationException::withMessages([
                'configuration' => ["No valid variant selected for package service [{$service->name}]."],
            ]);
        }

        if ($item->is_required && !$selected) {
            throw ValidationException::withMessages([
                'configuration' => ["Required package item [{$service->name}] cannot be deselected."],
            ]);
        }

        return [
            'id' => $item->id,
            'source_type' => 'linked',
            'label' => $service->name,
            'subtitle' => $item->subtitle ?: $service->short_description ?? $service->description,
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'service_type_id' => $service->service_type_id,
            'is_required' => (bool) $item->is_required,
            'is_default_selected' => (bool) $item->is_default_selected,
            'selected' => (bool) $selected,
            'selection_mode' => $variants->isNotEmpty() ? 'runtime_variant' : 'fixed_service',
            'requires_runtime_variant_selection' => $variants->isNotEmpty(),
            'selected_option_id' => $selectedVariant?->id,
            'selected_variant_id' => $selectedVariant?->id,
            'selected_option_label' => $selectedVariant?->name,
            'selected_price' => $selected
                ? (float) ($selectedVariant?->display_price ?? $service->display_price)
                : 0.0,
            'selected_duration_minutes' => $selected
                ? (int) ($selectedVariant?->resolved_duration_minutes ?? $service->resolved_duration_minutes ?? 0)
                : 0,
            'options' => [],
            'custom_price' => null,
            'custom_duration_minutes' => null,
        ];
    }

    protected function resolveCustomItemSelection(PackageItem $item, ?array $submittedItem): array
    {
        $options = $item->options
            ->sortBy(fn ($option) => sprintf('%08d-%08d', $option->sort_order, $option->id))
            ->values();
        $selected = $submittedItem['selected'] ?? $item->is_default_selected;

        if ($item->is_required) {
            $selected = true;
        }

        $selectedOption = null;
        if ($options->isNotEmpty()) {
            $requestedOptionId = $submittedItem['option_id'] ?? $submittedItem['variant_id'] ?? null;
            if ($requestedOptionId) {
                $selectedOption = $options->firstWhere('id', $requestedOptionId);
            }

            $selectedOption ??= $options->firstWhere('is_default', true);
            $selectedOption ??= ($selected ? $options->first() : null);
        }

        if ($selected && $options->isNotEmpty() && !$selectedOption) {
            throw ValidationException::withMessages([
                'configuration' => ["No valid option selected for package item [{$item->name}]."],
            ]);
        }

        if ($item->is_required && !$selected) {
            throw ValidationException::withMessages([
                'configuration' => ["Required package item [{$item->name}] cannot be deselected."],
            ]);
        }

        $selectedPrice = $selected
            ? (float) ($selectedOption?->price ?? $item->custom_price ?? 0)
            : 0.0;
        $selectedDuration = $selected
            ? (int) ($selectedOption?->duration_minutes ?? $item->custom_duration_minutes ?? 0)
            : 0;

        return [
            'id' => $item->id,
            'source_type' => 'custom',
            'label' => $item->name,
            'subtitle' => $item->subtitle,
            'service_id' => null,
            'service_slug' => null,
            'service_type_id' => null,
            'is_required' => (bool) $item->is_required,
            'is_default_selected' => (bool) $item->is_default_selected,
            'selected' => (bool) $selected,
            'selection_mode' => $options->isNotEmpty() ? 'manual_option' : 'fixed_custom',
            'requires_runtime_variant_selection' => false,
            'selected_option_id' => $selectedOption?->id,
            'selected_variant_id' => null,
            'selected_option_label' => $selectedOption?->name,
            'selected_price' => $selectedPrice,
            'selected_duration_minutes' => $selectedDuration,
            'options' => $options->map(fn (PackageItemOption $option) => [
                'id' => $option->id,
                'name' => $option->name,
                'subtitle' => $option->subtitle,
                'price' => (float) $option->price,
                'duration_minutes' => (int) $option->duration_minutes,
                'is_default' => (bool) $option->is_default,
                'sort_order' => $option->sort_order,
            ])->values()->all(),
            'custom_price' => $item->custom_price,
            'custom_duration_minutes' => $item->custom_duration_minutes,
        ];
    }

    protected function resolvePackagePricing(Package $package, float $selectedTotal): array
    {
        $threshold = $package->base_price_threshold ?? $package->price ?? 0;
        $discountType = $package->discount_type ?: (($package->discount_value ?? null) !== null ? 'percentage' : null);
        $discountValue = (float) ($package->discount_value ?? ($package->discount ?? 0));
        $discountApplied = $selectedTotal >= (float) $threshold;
        $discountAmount = 0.0;
        $discountPercentage = 0;

        if ($discountApplied && $discountType && $discountValue > 0) {
            if ($discountType === 'fixed') {
                $discountAmount = min($selectedTotal, $discountValue);
                $discountPercentage = $selectedTotal > 0
                    ? (int) round(($discountAmount / $selectedTotal) * 100)
                    : 0;
            } else {
                $discountPercentage = (int) round($discountValue);
                $discountAmount = round(($selectedTotal * $discountValue) / 100, 2);
            }
        }

        return [
            'base_price_threshold' => $threshold === null ? null : (float) $threshold,
            'discount_type' => $discountType,
            'discount_value' => $discountValue > 0 ? $discountValue : null,
            'discount_applied' => $discountApplied && $discountAmount > 0,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountApplied ? $discountPercentage : 0,
            'final_total' => max(0, round($selectedTotal - $discountAmount, 2)),
        ];
    }

    protected function previewItemsFromGroups(Collection $groups): array
    {
        return $groups
            ->map(function ($group) {
                $selectedLabels = collect($group['items'] ?? [])
                    ->where('selected', true)
                    ->pluck('label')
                    ->filter()
                    ->values();

                if ($selectedLabels->isEmpty()) {
                    return null;
                }

                return trim(($group['title'] ?? 'Package') . ': ' . $selectedLabels->implode(', '));
            })
            ->filter()
            ->take(3)
            ->values()
            ->all();
    }

    protected function serviceCandidatePayload(Service $service): array
    {
        $variants = $service->relationLoaded('activeVariants')
            ? $service->activeVariants
            : collect();

        return [
            'id' => $service->id,
            'title' => $service->name,
            'subtitle' => $service->short_description ?? $service->description,
            'price' => (float) $service->display_price,
            'duration_minutes' => (int) ($service->resolved_duration_minutes ?? 0),
            'has_variants' => $variants->isNotEmpty() || (bool) $service->has_variants,
            'variant_count' => $variants->count(),
        ];
    }
}
