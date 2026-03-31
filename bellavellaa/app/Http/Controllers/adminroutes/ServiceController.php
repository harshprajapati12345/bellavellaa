<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\Category::where('type', 'services')->orderBy('name')->get(['id', 'name']);
        $groups = \App\Models\ServiceGroup::orderBy('name')->get(['id', 'category_id', 'name']);
        $types = ServiceType::orderBy('name')->get(['id', 'service_group_id', 'name']);

        $services = Service::with(['category', 'serviceGroup', 'serviceType.serviceGroup.category', 'variants'])
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $categoryId = $request->integer('category_id');
                $query->where(function ($inner) use ($categoryId) {
                    $inner->where('category_id', $categoryId)
                        ->orWhereHas('serviceType.serviceGroup', fn ($serviceTypeQuery) => $serviceTypeQuery->where('category_id', $categoryId));
                });
            })
            ->when($request->filled('service_group_id'), function ($query) use ($request) {
                $groupId = $request->integer('service_group_id');
                $query->where(function ($inner) use ($groupId) {
                    $inner->where('service_group_id', $groupId)
                        ->orWhereHas('serviceType', fn ($serviceTypeQuery) => $serviceTypeQuery->where('service_group_id', $groupId));
                });
            })
            ->when($request->filled('service_type_id'), fn ($query) => $query->where('service_type_id', $request->integer('service_type_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->get();

        $totalServices = $services->count();
        $activeServices = $services->where('status', 'Active')->count();
        $inactiveServices = $totalServices - $activeServices;
        $mostBooked = $services->sortByDesc('bookings')->first();

        return view('services.index', compact(
            'services',
            'totalServices',
            'activeServices',
            'inactiveServices',
            'mostBooked',
            'categories',
            'groups',
            'types'
        ));
    }

    public function create()
    {
        $serviceTypes = ServiceType::with('serviceGroup.category')
            ->where('status', 'Active')
            ->whereHas('serviceGroup', fn ($query) => $query->where('status', 'Active')->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'Active')))
            ->orderBy('name')
            ->get();

        return view('services.create', compact('serviceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|integer|exists:service_types,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'nullable|integer|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:base_price',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'desc_title' => 'nullable|string|max:255',
            'has_variants' => 'nullable|boolean',
            'allow_direct_booking_with_variants' => 'nullable|boolean',
            'service_image' => 'nullable|image|max:2048',
        ]);

        $serviceType = ServiceType::with('serviceGroup.category')->findOrFail($validated['service_type_id']);
        $hasVariants = $request->boolean('has_variants');
        $basePrice = $validated['base_price'] ?? null;

        if (!$hasVariants && $basePrice === null) {
            return back()->withErrors(['base_price' => 'Base price is required for services without variants.'])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('service_image')) {
            $imagePath = $request->file('service_image')->store('services', 'public');
        }

        $slugBase = Str::slug($validated['name']);
        $slug = $slugBase;
        $counter = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        Service::create([
            'service_type_id' => $serviceType->id,
            'service_group_id' => $serviceType->service_group_id,
            'category_id' => $serviceType->serviceGroup?->category_id,
            'name' => $validated['name'],
            'slug' => $slug,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'duration' => $validated['duration_minutes'] ?? null,
            'base_price' => $basePrice,
            'sale_price' => $validated['sale_price'] ?? null,
            'price' => $basePrice,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['description'] ?? null,
            'long_description' => $validated['description'] ?? null,
            'desc_title' => $validated['desc_title'] ?? null,
            'service_types' => null,
            'has_variants' => $hasVariants,
            'is_bookable' => true,
            'allow_direct_booking_with_variants' => $request->boolean('allow_direct_booking_with_variants'),
            'status' => $request->input('form_action') === 'publish' ? 'Active' : 'Inactive',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image' => $imagePath,
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'category' => $service->resolved_category?->name ?? $service->category?->name,
            'group' => $service->resolved_service_group?->name ?? $service->serviceGroup?->name,
            'type' => $service->serviceType?->name,
            'price' => number_format($service->display_price),
            'duration' => $service->resolved_duration_minutes,
            'status' => $service->status,
            'description' => strip_tags($service->description ?? 'No description available.'),
            'image' => $service->image
                ? (str_starts_with($service->image, 'http') ? $service->image : asset('storage/' . $service->image))
                : null,
        ]);
    }

    public function edit(Service $service)
    {
        $serviceTypes = ServiceType::with('serviceGroup.category')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $service->load(['serviceType.serviceGroup.category', 'variants']);

        return view('services.edit', compact('service', 'serviceTypes'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|integer|exists:service_types,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'nullable|integer|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:base_price',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'desc_title' => 'nullable|string|max:255',
            'has_variants' => 'nullable|boolean',
            'allow_direct_booking_with_variants' => 'nullable|boolean',
            'service_image' => 'nullable|image|max:2048',
        ]);

        $serviceType = ServiceType::with('serviceGroup.category')->findOrFail($validated['service_type_id']);
        $hasVariants = $request->boolean('has_variants');
        $basePrice = $validated['base_price'] ?? null;

        if (!$hasVariants && $basePrice === null) {
            return back()->withErrors(['base_price' => 'Base price is required for services without variants.'])->withInput();
        }

        $imagePath = $service->image;
        if ($request->hasFile('service_image')) {
            $imagePath = $request->file('service_image')->store('services', 'public');
        }

        $service->update([
            'service_type_id' => $serviceType->id,
            'service_group_id' => $serviceType->service_group_id,
            'category_id' => $serviceType->serviceGroup?->category_id,
            'name' => $validated['name'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'duration' => $validated['duration_minutes'] ?? null,
            'base_price' => $basePrice,
            'sale_price' => $validated['sale_price'] ?? null,
            'price' => $basePrice,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['description'] ?? null,
            'long_description' => $validated['description'] ?? null,
            'desc_title' => $validated['desc_title'] ?? null,
            'service_types' => null,
            'has_variants' => $hasVariants,
            'is_bookable' => true,
            'allow_direct_booking_with_variants' => $request->boolean('allow_direct_booking_with_variants'),
            'status' => $request->input('form_action') === 'publish' ? 'Active' : 'Inactive',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image' => $imagePath,
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }

    public function toggleStatus(Request $request, Service $service)
    {
        $service->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
}
