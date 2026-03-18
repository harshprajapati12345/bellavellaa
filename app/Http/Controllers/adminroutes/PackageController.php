<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\PackageItem;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Services\ConfigurablePackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PackageController extends Controller
{
    public function __construct(
        protected ConfigurablePackageService $packageService
    ) {
    }

    public function index()
    {
        $packages = Package::with(['category', 'services'])->get();
        $allServices = Service::all()->pluck('name', 'id');

        $totalPackages = $packages->count();
        $activePackages = $packages->where('status', 'Active')->count();
        $totalBookings = $packages->sum('bookings');
        $topPackage = $packages->sortByDesc('bookings')->first();

        return view('packages.index', compact(
            'packages',
            'totalPackages',
            'activePackages',
            'totalBookings',
            'topPackage',
            'allServices'
        ));
    }

    public function create()
    {
        return view('packages.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $contexts = $this->normalizeContexts($validated['contexts'] ?? []);
        $groups = $this->normalizeGroups($validated['groups'] ?? []);

        DB::transaction(function () use ($request, $validated, $contexts, $groups) {
            $media = $this->storePackageMedia($request);

            $package = Package::create([
                'name' => $validated['name'],
                'slug' => $this->generateUniqueSlug($validated['name']),
                'category_id' => $this->resolvePrimaryCategoryId($contexts),
                'price' => $validated['package_price'],
                'base_price_threshold' => $validated['base_price_threshold'] ?? $validated['package_price'],
                'discount' => $validated['discount'] ?? 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? null,
                'duration' => $validated['duration'] ?? 0,
                'description' => $validated['desc_content'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'tag_label' => $validated['tag_label'] ?? null,
                'package_mode' => $validated['package_mode'] ?? 'hierarchy',
                'pricing_rule' => 'threshold_discount_over_selected_total',
                'duration_rule' => 'sum_selected_options',
                'is_configurable' => !empty($groups) || $request->boolean('is_configurable'),
                'quantity_allowed' => $request->boolean('quantity_allowed'),
                'desc_title' => $validated['desc_title'] ?? null,
                'desc_image' => $media['desc_image'],
                'aftercare_content' => $validated['aftercare_content'] ?? null,
                'aftercare_image' => $media['aftercare_image'],
                'status' => $request->input('form_action') === 'publish' ? 'Active' : 'Inactive',
                'featured' => $request->boolean('featured'),
                'sort_order' => $validated['sort_order'] ?? 0,
                'image' => $media['image'],
            ]);

            $this->syncLegacyServices($package, $validated['service_ids'] ?? []);
            $this->syncSharedEngine($package, $contexts, $groups);
        });

        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function show(Package $package)
    {
        $serviceNames = $package->services()->pluck('name')->toArray();

        return response()->json([
            'id' => $package->id,
            'name' => $package->name,
            'category' => $package->category?->name,
            'services' => $serviceNames,
            'price' => number_format($package->price),
            'base_price_threshold' => $package->base_price_threshold,
            'discount_type' => $package->discount_type,
            'discount_value' => $package->discount_value,
            'duration' => $package->duration,
            'status' => $package->status,
            'description' => $package->description,
            'desc_title' => $package->desc_title,
            'desc_image' => $package->desc_image,
            'aftercare_content' => $package->aftercare_content,
            'aftercare_image' => $package->aftercare_image,
            'image' => $package->image
                ? (str_starts_with($package->image, 'http') ? $package->image : asset('storage/' . $package->image))
                : null,
        ]);
    }

    public function edit(Package $package)
    {
        if ($this->sharedEngineAvailable()) {
            $package->loadMissing(['contexts', 'groups.items.options', 'groups.items.service', 'services']);
        } else {
            $package->loadMissing(['services']);
        }

        return view('packages.edit', $this->formData($package));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $this->validatePayload($request);
        $contexts = $this->normalizeContexts($validated['contexts'] ?? []);
        $groups = $this->normalizeGroups($validated['groups'] ?? []);

        DB::transaction(function () use ($request, $validated, $package, $contexts, $groups) {
            $media = $this->storePackageMedia($request, $package);

            $package->update([
                'name' => $validated['name'],
                'category_id' => $this->resolvePrimaryCategoryId($contexts),
                'price' => $validated['package_price'],
                'base_price_threshold' => $validated['base_price_threshold'] ?? $validated['package_price'],
                'discount' => $validated['discount'] ?? 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? null,
                'duration' => $validated['duration'] ?? 0,
                'description' => $validated['desc_content'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'tag_label' => $validated['tag_label'] ?? null,
                'package_mode' => $validated['package_mode'] ?? $package->package_mode ?? 'hierarchy',
                'pricing_rule' => 'threshold_discount_over_selected_total',
                'duration_rule' => 'sum_selected_options',
                'is_configurable' => !empty($groups) || $request->boolean('is_configurable'),
                'quantity_allowed' => $request->boolean('quantity_allowed'),
                'desc_title' => $validated['desc_title'] ?? null,
                'desc_image' => $media['desc_image'],
                'aftercare_content' => $validated['aftercare_content'] ?? null,
                'aftercare_image' => $media['aftercare_image'],
                'status' => $request->input('form_action') === 'publish' ? 'Active' : 'Inactive',
                'featured' => $request->boolean('featured'),
                'sort_order' => $validated['sort_order'] ?? $package->sort_order,
                'image' => $media['image'],
            ]);

            $this->syncLegacyServices($package, $validated['service_ids'] ?? []);
            $this->syncSharedEngine($package, $contexts, $groups);
        });

        return redirect()->route('packages.index')
            ->with('success', 'Package updated successfully!');
    }

    public function toggleStatus(Package $package)
    {
        $package->update(['status' => $package->status === 'Active' ? 'Inactive' : 'Active']);

        return response()->json(['success' => true]);
    }

    public function destroy(Package $package)
    {
        $package->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('packages.index')
            ->with('success', 'Package deleted.');
    }

    public function linkedGroups(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'context_type' => ['required', Rule::in(['category', 'service_group'])],
            'context_id' => ['required', 'integer'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->packageService->groupCandidatesForContext(
                $validated['context_type'],
                (int) $validated['context_id']
            )->values()->all(),
        ]);
    }

    public function linkedGroupItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_type' => ['required', Rule::in(['service_group', 'service_type'])],
            'group_id' => ['required', 'integer'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->packageService->itemCandidatesForLinkedGroup(
                $validated['group_type'],
                (int) $validated['group_id']
            )->values()->all(),
        ]);
    }

    protected function formData(?Package $package = null): array
    {
        $services = Service::where('status', 'Active')->orderBy('name')->get(['id', 'name', 'price', 'duration']);
        $categories = Category::where('type', 'packages')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);
        $serviceGroups = ServiceGroup::where('status', 'Active')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'category_id']);
        $selectedServiceIds = $package?->services?->pluck('id')->all() ?? [];
        $sharedEngineAvailable = $this->sharedEngineAvailable();

        return compact(
            'package',
            'services',
            'categories',
            'serviceGroups',
            'selectedServiceIds',
            'sharedEngineAvailable'
        );
    }

    protected function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'package_price' => ['required', 'numeric', 'min:0'],
            'base_price_threshold' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'short_description' => ['nullable', 'string'],
            'tag_label' => ['nullable', 'string', 'max:100'],
            'package_mode' => ['required', Rule::in(['hierarchy', 'hybrid', 'manual'])],
            'desc_title' => ['nullable', 'string', 'max:255'],
            'desc_content' => ['nullable', 'string'],
            'aftercare_content' => ['nullable', 'string'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'contexts' => ['nullable', 'array'],
            'contexts.*.id' => ['nullable', 'integer'],
            'contexts.*.context_type' => ['nullable', Rule::in(['category', 'service_group'])],
            'contexts.*.context_id' => ['nullable', 'integer'],
            'contexts.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'groups' => ['nullable', 'array'],
            'groups.*.id' => ['nullable', 'integer'],
            'groups.*.source_type' => ['nullable', Rule::in(['linked', 'custom'])],
            'groups.*.linked_type' => ['nullable', Rule::in(['service_group', 'service_type'])],
            'groups.*.linked_id' => ['nullable', 'integer'],
            'groups.*.title' => ['nullable', 'string', 'max:255'],
            'groups.*.subtitle' => ['nullable', 'string', 'max:255'],
            'groups.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'groups.*.items' => ['nullable', 'array'],
            'groups.*.items.*.id' => ['nullable', 'integer'],
            'groups.*.items.*.source_type' => ['nullable', Rule::in(['linked', 'custom'])],
            'groups.*.items.*.service_id' => ['nullable', 'integer', 'exists:services,id'],
            'groups.*.items.*.name' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.subtitle' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.custom_price' => ['nullable', 'numeric', 'min:0'],
            'groups.*.items.*.custom_duration_minutes' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.is_required' => ['nullable'],
            'groups.*.items.*.is_default_selected' => ['nullable'],
            'groups.*.items.*.options' => ['nullable', 'array'],
            'groups.*.items.*.options.*.id' => ['nullable', 'integer'],
            'groups.*.items.*.options.*.name' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.options.*.subtitle' => ['nullable', 'string', 'max:255'],
            'groups.*.items.*.options.*.price' => ['nullable', 'numeric', 'min:0'],
            'groups.*.items.*.options.*.duration_minutes' => ['nullable', 'integer', 'min:0'],
            'groups.*.items.*.options.*.is_default' => ['nullable'],
            'groups.*.items.*.options.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!$this->sharedEngineAvailable()) {
            return $validated;
        }

        if ($this->normalizeContexts($validated['contexts'] ?? []) === []) {
            throw ValidationException::withMessages([
                'contexts' => 'Add at least one package context. Use Category for Bridal or Service Group for Luxe/Prime/Ayurveda.',
            ]);
        }

        return $validated;
    }

    protected function normalizeContexts(array $contexts): array
    {
        $normalized = [];

        foreach ($contexts as $index => $context) {
            $type = $context['context_type'] ?? null;
            $id = isset($context['context_id']) ? (int) $context['context_id'] : 0;

            if (!$type || $id <= 0) {
                continue;
            }

            $exists = match ($type) {
                'category' => Category::whereKey($id)->where('type', 'packages')->exists(),
                'service_group' => ServiceGroup::whereKey($id)->exists(),
                default => false,
            };

            if (!$exists) {
                throw ValidationException::withMessages([
                    "contexts.{$index}.context_id" => 'Selected context record does not exist.',
                ]);
            }

            $normalized[$type . ':' . $id] = [
                'id' => isset($context['id']) ? (int) $context['id'] : null,
                'context_type' => $type,
                'context_id' => $id,
                'sort_order' => isset($context['sort_order']) ? (int) $context['sort_order'] : count($normalized),
            ];
        }

        return array_values($normalized);
    }

    protected function normalizeGroups(array $groups): array
    {
        $normalizedGroups = [];
        $mode = request()->input('package_mode', 'hierarchy');

        foreach ($groups as $groupIndex => $group) {
            $sourceType = $group['source_type'] ?? 'custom';
            $linkedType = $group['linked_type'] ?? null;
            $linkedId = isset($group['linked_id']) ? (int) $group['linked_id'] : null;
            $title = trim((string) ($group['title'] ?? ''));
            $items = $group['items'] ?? [];
            $normalizedItems = [];

            if ($sourceType === 'linked' && !in_array($linkedType, ['service_group', 'service_type'], true)) {
                throw ValidationException::withMessages([
                    "groups.{$groupIndex}.linked_type" => 'Linked group type is required.',
                ]);
            }

            if ($sourceType === 'linked' && ($linkedId ?? 0) <= 0) {
                throw ValidationException::withMessages([
                    "groups.{$groupIndex}.linked_id" => 'Linked group selection is required.',
                ]);
            }

            foreach ($items as $itemIndex => $item) {
                $itemSourceType = $item['source_type'] ?? ($sourceType === 'linked' ? 'linked' : 'custom');
                $serviceId = isset($item['service_id']) ? (int) $item['service_id'] : null;
                $name = trim((string) ($item['name'] ?? ''));
                $options = $item['options'] ?? [];
                $normalizedOptions = [];
                $service = null;

                foreach ($options as $optionIndex => $option) {
                    $optionName = trim((string) ($option['name'] ?? ''));

                    if ($optionName === '') {
                        continue;
                    }

                    $normalizedOptions[] = [
                        'id' => isset($option['id']) ? (int) $option['id'] : null,
                        'name' => $optionName,
                        'subtitle' => $option['subtitle'] ?? null,
                        'price' => isset($option['price']) ? (float) $option['price'] : 0.0,
                        'duration_minutes' => isset($option['duration_minutes']) ? (int) $option['duration_minutes'] : 0,
                        'is_default' => !empty($option['is_default']),
                        'sort_order' => isset($option['sort_order']) ? (int) $option['sort_order'] : $optionIndex,
                    ];
                }

                if ($itemSourceType === 'linked') {
                    if (($serviceId ?? 0) <= 0) {
                        throw ValidationException::withMessages([
                            "groups.{$groupIndex}.items.{$itemIndex}.service_id" => 'Linked item must reference a service.',
                        ]);
                    }

                    $service = Service::find($serviceId);
                    if (!$service) {
                        throw ValidationException::withMessages([
                            "groups.{$groupIndex}.items.{$itemIndex}.service_id" => 'Linked service was not found.',
                        ]);
                    }

                    if ($sourceType === 'linked' && !$this->serviceBelongsToLinkedGroup($service, $linkedType, $linkedId)) {
                        throw ValidationException::withMessages([
                            "groups.{$groupIndex}.items.{$itemIndex}.service_id" => 'Selected service does not belong to the linked group.',
                        ]);
                    }
                } else {
                    if ($mode === 'hierarchy') {
                        throw ValidationException::withMessages([
                            "groups.{$groupIndex}.items.{$itemIndex}.source_type" => 'Hierarchy packages cannot contain custom items.',
                        ]);
                    }

                    if ($name === '' && $normalizedOptions === [] && empty($item['custom_price']) && empty($item['custom_duration_minutes'])) {
                        continue;
                    }

                    if ($name === '') {
                        throw ValidationException::withMessages([
                            "groups.{$groupIndex}.items.{$itemIndex}.name" => 'Custom item title is required.',
                        ]);
                    }
                }

                $normalizedItems[] = [
                    'id' => isset($item['id']) ? (int) $item['id'] : null,
                    'source_type' => $itemSourceType,
                    'service_id' => $itemSourceType === 'linked' ? $serviceId : null,
                    'name' => $itemSourceType === 'linked' ? ($service->name ?? $name) : $name,
                    'subtitle' => $item['subtitle'] ?? null,
                    'custom_price' => $itemSourceType === 'custom' && isset($item['custom_price']) ? (float) $item['custom_price'] : null,
                    'custom_duration_minutes' => $itemSourceType === 'custom' && isset($item['custom_duration_minutes']) ? (int) $item['custom_duration_minutes'] : null,
                    'is_required' => !empty($item['is_required']),
                    'is_default_selected' => !empty($item['is_default_selected']),
                    'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : $itemIndex,
                    'options' => $itemSourceType === 'custom' ? $normalizedOptions : [],
                ];
            }

            if ($sourceType === 'linked' && $normalizedItems === []) {
                continue;
            }

            if ($sourceType === 'custom' && $mode === 'hierarchy') {
                throw ValidationException::withMessages([
                    "groups.{$groupIndex}.source_type" => 'Hierarchy packages cannot contain custom groups.',
                ]);
            }

            if ($sourceType === 'custom' && $title === '' && $normalizedItems === []) {
                continue;
            }

            if ($sourceType === 'custom' && $title === '') {
                throw ValidationException::withMessages([
                    "groups.{$groupIndex}.title" => 'Custom group title is required.',
                ]);
            }

            $normalizedGroups[] = [
                'id' => isset($group['id']) ? (int) $group['id'] : null,
                'source_type' => $sourceType,
                'linked_type' => $sourceType === 'linked' ? $linkedType : null,
                'linked_id' => $sourceType === 'linked' ? $linkedId : null,
                'title' => $title,
                'subtitle' => $group['subtitle'] ?? null,
                'sort_order' => isset($group['sort_order']) ? (int) $group['sort_order'] : $groupIndex,
                'items' => $normalizedItems,
            ];
        }

        if ($mode === 'manual') {
            foreach ($normalizedGroups as $group) {
                if (($group['source_type'] ?? 'custom') !== 'custom') {
                    throw ValidationException::withMessages([
                        'groups' => 'Manual packages can only contain custom groups and items.',
                    ]);
                }
            }
        }

        return $normalizedGroups;
    }

    protected function storePackageMedia(Request $request, ?Package $package = null): array
    {
        $imagePath = $package?->image;
        if ($request->hasFile('package_image')) {
            $stored = $request->file('package_image')->store('packages', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $descImagePath = $package?->desc_image;
        if ($request->hasFile('desc_image')) {
            $storedDesc = $request->file('desc_image')->store('packages/desc', 'public');
            $descImagePath = asset('storage/' . $storedDesc);
        }

        $afterImagePath = $package?->aftercare_image;
        if ($request->hasFile('aftercare_image')) {
            $storedAfter = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $afterImagePath = asset('storage/' . $storedAfter);
        }

        return [
            'image' => $imagePath,
            'desc_image' => $descImagePath,
            'aftercare_image' => $afterImagePath,
        ];
    }

    protected function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Package::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected function resolvePrimaryCategoryId(array $contexts): ?int
    {
        foreach ($contexts as $context) {
            if (($context['context_type'] ?? null) === 'category') {
                return (int) $context['context_id'];
            }
        }

        return null;
    }

    protected function syncLegacyServices(Package $package, array $serviceIds): void
    {
        $validIds = Service::whereIn('id', $serviceIds)->pluck('id')->all();
        $package->services()->sync($validIds);
    }

    protected function syncSharedEngine(Package $package, array $contexts, array $groups): void
    {
        if (!$this->sharedEngineAvailable()) {
            return;
        }

        $this->syncContexts($package, $contexts);
        $this->syncGroups($package, $groups);
    }

    protected function syncContexts(Package $package, array $contexts): void
    {
        $existing = $package->contexts()->get()->keyBy('id');
        $keepIds = [];

        foreach ($contexts as $context) {
            if (!empty($context['id']) && $existing->has($context['id'])) {
                $record = $existing->get($context['id']);
                $record->update([
                    'context_type' => $context['context_type'],
                    'context_id' => $context['context_id'],
                    'sort_order' => $context['sort_order'],
                ]);
            } else {
                $record = $package->contexts()->create([
                    'context_type' => $context['context_type'],
                    'context_id' => $context['context_id'],
                    'sort_order' => $context['sort_order'],
                ]);
            }

            $keepIds[] = $record->id;
        }

        $query = $package->contexts();
        if ($keepIds !== []) {
            $query->whereNotIn('id', $keepIds);
        }
        $query->delete();
    }

    protected function syncGroups(Package $package, array $groups): void
    {
        $package->loadMissing('groups.items.options');
        $existingGroups = $package->groups->keyBy('id');
        $keepGroupIds = [];

        foreach ($groups as $groupData) {
            if (!empty($groupData['id']) && $existingGroups->has($groupData['id'])) {
                $group = $existingGroups->get($groupData['id']);
                $group->update([
                    'source_type' => $groupData['source_type'],
                    'linked_type' => $groupData['linked_type'],
                    'linked_id' => $groupData['linked_id'],
                    'title' => $groupData['title'],
                    'subtitle' => $groupData['subtitle'],
                    'sort_order' => $groupData['sort_order'],
                ]);
            } else {
                $group = $package->groups()->create([
                    'source_type' => $groupData['source_type'],
                    'linked_type' => $groupData['linked_type'],
                    'linked_id' => $groupData['linked_id'],
                    'title' => $groupData['title'],
                    'subtitle' => $groupData['subtitle'],
                    'sort_order' => $groupData['sort_order'],
                ]);
            }

            $keepGroupIds[] = $group->id;
            $this->syncItems($group, $groupData['items'] ?? []);
        }

        $query = $package->groups();
        if ($keepGroupIds !== []) {
            $query->whereNotIn('id', $keepGroupIds);
        }
        $query->delete();
    }

    protected function syncItems(PackageGroup $group, array $items): void
    {
        $group->loadMissing('items.options');
        $existingItems = $group->items->keyBy('id');
        $keepItemIds = [];

        foreach ($items as $itemData) {
            if (!empty($itemData['id']) && $existingItems->has($itemData['id'])) {
                $item = $existingItems->get($itemData['id']);
                $item->update([
                    'source_type' => $itemData['source_type'],
                    'service_id' => $itemData['service_id'],
                    'name' => $itemData['name'],
                    'subtitle' => $itemData['subtitle'],
                    'custom_price' => $itemData['custom_price'],
                    'custom_duration_minutes' => $itemData['custom_duration_minutes'],
                    'is_required' => $itemData['is_required'],
                    'is_default_selected' => $itemData['is_default_selected'],
                    'sort_order' => $itemData['sort_order'],
                ]);
            } else {
                $item = $group->items()->create([
                    'source_type' => $itemData['source_type'],
                    'service_id' => $itemData['service_id'],
                    'name' => $itemData['name'],
                    'subtitle' => $itemData['subtitle'],
                    'custom_price' => $itemData['custom_price'],
                    'custom_duration_minutes' => $itemData['custom_duration_minutes'],
                    'is_required' => $itemData['is_required'],
                    'is_default_selected' => $itemData['is_default_selected'],
                    'sort_order' => $itemData['sort_order'],
                ]);
            }

            $keepItemIds[] = $item->id;
            $this->syncOptions($item, $itemData['options'] ?? []);
        }

        $query = $group->items();
        if ($keepItemIds !== []) {
            $query->whereNotIn('id', $keepItemIds);
        }
        $query->delete();
    }

    protected function syncOptions(PackageItem $item, array $options): void
    {
        $item->loadMissing('options');
        $existingOptions = $item->options->keyBy('id');
        $keepOptionIds = [];

        foreach ($options as $optionData) {
            if (!empty($optionData['id']) && $existingOptions->has($optionData['id'])) {
                $option = $existingOptions->get($optionData['id']);
                $option->update([
                    'name' => $optionData['name'],
                    'subtitle' => $optionData['subtitle'],
                    'price' => $optionData['price'],
                    'duration_minutes' => $optionData['duration_minutes'],
                    'is_default' => $optionData['is_default'],
                    'sort_order' => $optionData['sort_order'],
                    'service_id' => null,
                    'service_variant_id' => null,
                ]);
            } else {
                $option = $item->options()->create([
                    'name' => $optionData['name'],
                    'subtitle' => $optionData['subtitle'],
                    'price' => $optionData['price'],
                    'duration_minutes' => $optionData['duration_minutes'],
                    'is_default' => $optionData['is_default'],
                    'sort_order' => $optionData['sort_order'],
                    'service_id' => null,
                    'service_variant_id' => null,
                ]);
            }

            $keepOptionIds[] = $option->id;
        }

        $query = $item->options();
        if ($keepOptionIds !== []) {
            $query->whereNotIn('id', $keepOptionIds);
        }
        $query->delete();
    }

    protected function serviceBelongsToLinkedGroup(Service $service, string $linkedType, ?int $linkedId): bool
    {
        if (($linkedId ?? 0) <= 0) {
            return false;
        }

        return match ($linkedType) {
            'service_type' => (int) $service->service_type_id === (int) $linkedId,
            'service_group' => (int) ($service->service_group_id ?: $service->serviceType?->service_group_id) === (int) $linkedId,
            default => false,
        };
    }

    protected function sharedEngineAvailable(): bool
    {
        return Schema::hasTable('package_contexts')
            && Schema::hasTable('package_groups')
            && Schema::hasTable('package_items')
            && Schema::hasTable('package_item_options');
    }
}
