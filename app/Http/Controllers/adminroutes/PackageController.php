<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Category;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages    = Package::with('category')->get();
        $allServices = Service::all()->pluck('name', 'id');

        $totalPackages  = $packages->count();
        $activePackages = $packages->where('status', 'Active')->count();
        $totalBookings  = $packages->sum('bookings');
        $topPackage     = $packages->sortByDesc('bookings')->first();

        return view('packages.index', compact(
            'packages', 'totalPackages', 'activePackages', 'totalBookings', 'topPackage', 'allServices'
        ));
    }

    public function create()
    {
        $services = Service::where('status', 'Active')->orderBy('name')->get();
        // Only packages-type categories
        $categories = Category::where('type', 'packages')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();
        return view('packages.create', compact('services', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'package_price' => 'required|numeric|min:0',
            'sort_order'    => 'nullable|integer|min:0',
        ]);

        // Integrity: category must be packages-type
        $category = Category::whereKey($request->category_id)
            ->where('type', 'packages')
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('package_image')) {
            $stored    = $request->file('package_image')->store('packages', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $descImagePath = null;
        if ($request->hasFile('desc_image')) {
            $storedDesc    = $request->file('desc_image')->store('packages/desc', 'public');
            $descImagePath = asset('storage/' . $storedDesc);
        }

        $afterImagePath = null;
        if ($request->hasFile('aftercare_image')) {
            $storedAfter    = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $afterImagePath = asset('storage/' . $storedAfter);
        }

        // Generate slug
        $base = Str::slug($request->name);
        $slug = $base;
        $i = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $package = Package::create([
            'name'              => $request->name,
            'slug'              => $slug,
            'category_id'       => $category->id,
            'price'             => $request->package_price,
            'discount'          => $request->discount ?? 0,
            'duration'          => $request->duration ?? 0,
            'description'       => $request->desc_content,
            'desc_title'        => $request->desc_title,
            'desc_image'        => $descImagePath,
            'aftercare_content' => $request->aftercare_content,
            'aftercare_image'   => $afterImagePath,
            'status'            => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'featured'          => $request->has('featured') ? 1 : 0,
            'sort_order'        => $request->sort_order ?? 0,
            'image'             => $imagePath,
        ]);

        // Sync services via pivot — filter to valid service IDs only
        if ($request->filled('service_ids')) {
            $validIds = Service::whereIn('id', (array) $request->service_ids)->pluck('id')->all();
            $package->services()->sync($validIds);
        }

        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function show(Package $package)
    {
        $serviceNames = $package->services()->pluck('name')->toArray();

        return response()->json([
            'id'                => $package->id,
            'name'              => $package->name,
            'category'          => $package->category?->name,
            'services'          => $serviceNames,
            'price'             => number_format($package->price),
            'discount'          => $package->discount,
            'final_price'       => number_format($package->price - ($package->price * ($package->discount / 100))),
            'duration'          => $package->duration,
            'status'            => $package->status,
            'description'       => $package->description,
            'desc_title'        => $package->desc_title,
            'desc_image'        => $package->desc_image,
            'aftercare_content' => $package->aftercare_content,
            'aftercare_image'   => $package->aftercare_image,
            'image'             => $package->image
                ? (str_starts_with($package->image, 'http') ? $package->image : asset('storage/' . $package->image))
                : null,
        ]);
    }

    public function edit(Package $package)
    {
        $services = Service::where('status', 'Active')->orderBy('name')->get();
        $categories = Category::where('type', 'packages')
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();
        $selectedServiceIds = $package->services()->pluck('services.id')->toArray();
        return view('packages.edit', compact('package', 'services', 'categories', 'selectedServiceIds'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'sort_order'    => 'nullable|integer|min:0',
        ]);

        $category = Category::whereKey($request->category_id)
            ->where('type', 'packages')
            ->firstOrFail();

        $imagePath = $package->image;
        if ($request->hasFile('package_image')) {
            $stored    = $request->file('package_image')->store('packages', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $descImagePath = $package->desc_image;
        if ($request->hasFile('desc_image')) {
            $storedDesc    = $request->file('desc_image')->store('packages/desc', 'public');
            $descImagePath = asset('storage/' . $storedDesc);
        }

        $afterImagePath = $package->aftercare_image;
        if ($request->hasFile('aftercare_image')) {
            $storedAfter    = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $afterImagePath = asset('storage/' . $storedAfter);
        }

        $package->update([
            'name'              => $request->name,
            'category_id'       => $category->id,
            'price'             => $request->package_price ?? $package->price,
            'discount'          => $request->discount ?? $package->discount,
            'duration'          => $request->duration ?? $package->duration,
            'description'       => $request->desc_content ?? $package->description,
            'desc_title'        => $request->desc_title ?? $package->desc_title,
            'desc_image'        => $descImagePath,
            'aftercare_content' => $request->aftercare_content ?? $package->aftercare_content,
            'aftercare_image'   => $afterImagePath,
            'status'            => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'featured'          => $request->has('featured') ? 1 : ($request->has('featured_hidden') ? 0 : $package->featured),
            'sort_order'        => $request->sort_order ?? $package->sort_order,
            'image'             => $imagePath,
        ]);

        // Sync pivot — only valid service IDs
        $rawIds  = (array) ($request->service_ids ?? []);
        $validIds = Service::whereIn('id', $rawIds)->pluck('id')->all();
        $package->services()->sync($validIds);

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
}
