<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category', 'serviceGroup')->get();

        $totalServices    = $services->count();
        $activeServices   = $services->where('status', 'Active')->count();
        $inactiveServices = $totalServices - $activeServices;
        $mostBooked       = $services->sortByDesc('bookings')->first();

        $categories = Category::where('type', 'services')->get();

        return view('services.index', compact(
            'services', 'totalServices', 'activeServices', 'inactiveServices', 'mostBooked', 'categories'
        ));
    }

    public function create()
    {
        // Only service-type categories in the dropdown
        $categories = Category::where('type', 'services')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();
        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => ['required', Rule::in(Category::where('type', 'services')->pluck('id'))],
            'service_group_id' => 'nullable|exists:service_groups,id',
            'sort_order'       => 'nullable|integer|min:0',
            'has_variants'     => 'nullable|boolean',
        ]);

        // Integrity: category must be services-type
        $category = Category::whereKey($request->category_id)
            ->where('type', 'services')
            ->firstOrFail();

        // Integrity: if a group is selected, it must belong to the selected category
        if ($request->filled('service_group_id')) {
            ServiceGroup::whereKey($request->service_group_id)
                ->where('category_id', $category->id)
                ->firstOrFail();
        }

        // Handle main image upload
        $imagePath = null;
        if ($request->hasFile('service_image')) {
            $stored    = $request->file('service_image')->store('services', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        // Build service_types JSON from parallel arrays
        $serviceTypes = [];
        $names   = $request->input('service_types', []);
        $prices  = $request->input('service_prices', []);
        $reviews = $request->input('service_reviews', []);
        foreach ($names as $i => $name) {
            if (trim($name) !== '') {
                $serviceTypes[] = [
                    'name'    => trim($name),
                    'price'   => $prices[$i] ?? 0,
                    'reviews' => $reviews[$i] ?? 0,
                ];
            }
        }

        $mainPrice = $request->price ?? ($serviceTypes[0]['price'] ?? 0);

        // Generate slug
        $base = Str::slug($request->name);
        $slug = $base;
        $i = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        Service::create([
            'name'             => $request->name,
            'slug'             => $slug,
            'category_id'      => $category->id,
            'service_group_id' => $request->service_group_id,
            'duration'         => $request->duration ?? 0,
            'price'            => $mainPrice,
            'description'      => $request->description,
            'desc_title'       => $request->desc_title,
            'service_types'    => !empty($serviceTypes) ? json_encode($serviceTypes) : null,
            'status'           => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'has_variants'     => $request->has('has_variants'),
            'featured'         => $request->has('featured') ? 1 : 0,
            'sort_order'       => $request->sort_order ?? 0,
            'image'            => $imagePath,
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        return response()->json([
            'id'          => $service->id,
            'name'        => $service->name,
            'category'    => $service->category?->name,
            'group'       => $service->serviceGroup?->name,
            'price'       => number_format($service->price),
            'duration'    => $service->duration,
            'status'      => $service->status,
            'description' => strip_tags($service->description ?? 'No description available.'),
            'image'       => $service->image
                ? (str_starts_with($service->image, 'http') ? $service->image : asset('storage/' . $service->image))
                : null,
        ]);
    }

    public function edit(Service $service)
    {
        $categories = Category::where('type', 'services')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        // Pre-load groups for the currently selected category
        $serviceGroups = $service->category_id
            ? ServiceGroup::where('category_id', $service->category_id)
                ->where('status', 'Active')
                ->orderBy('sort_order')
                ->get()
            : collect();

        $service->load('variants');

        return view('services.edit', compact('service', 'categories', 'serviceGroups'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => ['required', Rule::in(Category::where('type', 'services')->pluck('id'))],
            'service_group_id' => 'nullable|exists:service_groups,id',
            'sort_order'       => 'nullable|integer|min:0',
            'has_variants'     => 'nullable|boolean',
        ]);

        $category = Category::whereKey($request->category_id)
            ->where('type', 'services')
            ->firstOrFail();

        if ($request->filled('service_group_id')) {
            ServiceGroup::whereKey($request->service_group_id)
                ->where('category_id', $category->id)
                ->firstOrFail();
        }

        $imagePath = $service->image;
        if ($request->hasFile('service_image')) {
            $stored    = $request->file('service_image')->store('services', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $serviceTypes = [];
        $names   = $request->input('service_types', []);
        $prices  = $request->input('service_prices', []);
        $reviews = $request->input('service_reviews', []);
        foreach ($names as $i => $name) {
            if (trim($name) !== '') {
                $serviceTypes[] = [
                    'name'    => trim($name),
                    'price'   => $prices[$i] ?? 0,
                    'reviews' => $reviews[$i] ?? 0,
                ];
            }
        }

        $mainPrice = $request->price ?? ($serviceTypes[0]['price'] ?? $service->price);

        $service->update([
            'name'             => $request->name,
            'category_id'      => $category->id,
            'service_group_id' => $request->service_group_id,
            'duration'         => $request->duration ?? $service->duration,
            'price'            => $mainPrice,
            'description'      => $request->description,
            'desc_title'       => $request->desc_title,
            'service_types'    => !empty($serviceTypes) ? json_encode($serviceTypes) : null,
            'status'           => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'has_variants'     => $request->has('has_variants'),
            'sort_order'       => $request->sort_order ?? $service->sort_order,
            'image'            => $imagePath,
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')
            ->with('success', 'Service deleted.');
    }

    public function toggleStatus(Request $request, Service $service)
    {
        $service->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
}
