<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        $totalPackages  = $packages->count();
        $activePackages = $packages->where('status', 'Active')->count();
        $totalBookings  = $packages->sum('bookings');
        $topPackage     = $packages->sortByDesc('bookings')->first();

        return view('packages.index', compact(
            'packages', 'totalPackages', 'activePackages', 'totalBookings', 'topPackage'
        ));
    }

    public function create()
    {
        $services = Service::where('status', 'Active')->get();
        return view('packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'package_price' => 'required',
        ]);

        $imagePath = null;
        if ($request->hasFile('package_image')) {
            $stored = $request->file('package_image')->store('packages', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $descImagePath = null;
        if ($request->hasFile('desc_image')) {
            $storedDesc = $request->file('desc_image')->store('packages/desc', 'public');
            $descImagePath = asset('storage/' . $storedDesc);
        }

        $afterImagePath = null;
        if ($request->hasFile('aftercare_image')) {
            $storedAfter = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $afterImagePath = asset('storage/' . $storedAfter);
        }

        Package::create([
            'name'              => $request->name,
            'category'          => $request->category,
            'services'          => $request->service_ids, // Cast handles array to JSON
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
            'image'             => $imagePath,
        ]);

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    public function show(Package $package)
    {
        $services = is_array($package->services) ? $package->services : (is_string($package->services) ? json_decode($package->services, true) ?? [] : []);
        
        return response()->json([
            'id' => $package->id,
            'name' => $package->name,
            'services' => $services,
            'price' => number_format($package->price),
            'status' => $package->status,
            'image' => $package->image ? (str_starts_with($package->image, 'http') ? $package->image : asset('storage/'.$package->image)) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80',
        ]);
    }

    public function edit(Package $package)
    {
        $services = Service::where('status', 'Active')->get();
        return view('packages.edit', compact('package', 'services'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $imagePath = $package->image;
        if ($request->hasFile('package_image')) {
            $stored = $request->file('package_image')->store('packages', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $descImagePath = $package->desc_image;
        if ($request->hasFile('desc_image')) {
            $storedDesc = $request->file('desc_image')->store('packages/desc', 'public');
            $descImagePath = asset('storage/' . $storedDesc);
        }

        $afterImagePath = $package->aftercare_image;
        if ($request->hasFile('aftercare_image')) {
            $storedAfter = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $afterImagePath = asset('storage/' . $storedAfter);
        }

        $package->update([
            'name'              => $request->name,
            'category'          => $request->category,
            'services'          => $request->service_ids ?? $package->services,
            'price'             => $request->package_price ?? $package->price,
            'discount'          => $request->discount ?? $package->discount,
            'duration'          => $request->duration ?? $package->duration,
            'description'       => $request->desc_content ?? $package->description,
            'desc_title'        => $request->desc_title ?? $package->desc_title,
            'desc_image'        => $descImagePath,
            'aftercare_content' => $request->aftercare_content ?? $package->aftercare_content,
            'aftercare_image'   => $afterImagePath,
            'status'            => $request->form_action === 'publish' ? 'Active' : 'Inactive',
            'featured'          => $request->has('featured') ? 1 : 0,
            'image'             => $imagePath,
        ]);

        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }

    public function toggleStatus(Package $package)
    {
        $package->update(['status' => $package->status === 'Active' ? 'Inactive' : 'Active']);
        return response()->json(['success' => true]);
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted.');
    }
}
