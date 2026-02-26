<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->get();

        $totalServices   = $services->count();
        $activeServices  = $services->where('status', 'Active')->count();
        $inactiveServices = $totalServices - $activeServices;
        $mostBooked = $services->sortByDesc('bookings')->first();

        $categories = Category::withCount(['services', 'bookings'])->get();

        $totalCats     = $categories->count();
        $totalSvcs     = $categories->sum('services_count');
        $totalBookings = $categories->sum('bookings_count');
        $totalActive   = $categories->where('status', 'Active')->count();
        $topCategory   = $categories->sortByDesc('bookings_count')->first();

        $categories = Category::all();

        return view('services.index', compact(
            'services', 'totalServices', 'activeServices', 'inactiveServices', 'mostBooked', 'categories'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = ['Threading', 'Waxing', 'Facial', 'Cleanup', 'Bleach', 'Manicure', 'Pedicure', 'Hair Color', 'Hair Spa', 'Massage'];
        return view('services.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Handle main image upload
        $imagePath = null;
        if ($request->hasFile('service_image')) {
            $stored = $request->file('service_image')->store('services', 'public');
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

        // Use the first service type price as the main price if no separate price provided
        $mainPrice = $request->price ?? ($serviceTypes[0]['price'] ?? 0);

        Service::create([
            'name'          => $request->name,
            'category_id'   => $request->category_id,
            'category'      => Category::find($request->category_id)?->name,
            'subcategory'   => $request->subcategory,
            'duration'      => $request->duration ?? 0,
            'price'         => $mainPrice,
            'description'   => $request->description,
            'desc_title'    => $request->desc_title,
            'service_types' => !empty($serviceTypes) ? json_encode($serviceTypes) : null,
            'status'        => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'featured'      => $request->has('featured') ? 1 : 0,
            'image'         => $imagePath,
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'category' => $service->category,
            'price' => number_format($service->price),
            'duration' => $service->duration,
            'status' => $service->status,
            'description' => strip_tags($service->description ?? 'No description available.'),
            'image' => $service->image ? (str_starts_with($service->image, 'http') ? $service->image : asset('storage/'.$service->image)) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80',
        ]);
    }

    public function edit(Service $service)
    {
        $categories = Category::all();
        $subcategories = ['Threading', 'Waxing', 'Facial', 'Cleanup', 'Bleach', 'Manicure', 'Pedicure', 'Hair Color', 'Hair Spa', 'Massage'];
        return view('services.edit', compact('service', 'categories', 'subcategories'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $imagePath = $service->image;
        if ($request->hasFile('service_image')) {
            $stored = $request->file('service_image')->store('services', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        // Build service_types JSON
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

        // Use the first service type price as the main price if no separate price provided
        $mainPrice = $request->price ?? ($serviceTypes[0]['price'] ?? $service->price);

        $service->update([
            'name'          => $request->name,
            'category_id'   => $request->category_id,
            'category'      => Category::find($request->category_id)?->name,
            'subcategory'   => $request->subcategory,
            'duration'      => $request->duration ?? $service->duration,
            'price'         => $mainPrice,
            'description'   => $request->description,
            'desc_title'    => $request->desc_title,
            'service_types' => !empty($serviceTypes) ? json_encode($serviceTypes) : null,
            'status'        => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'image'         => $imagePath,
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
