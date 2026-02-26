<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Package;
use App\Http\Resources\Api\PackageResource;
use App\Http\Requests\Api\Admin\StorePackageRequest;
use App\Http\Requests\Api\Admin\UpdatePackageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PackageController extends BaseController
{
    /**
     * Display a listing of the packages.
     */
    public function index(): JsonResponse
    {
        $query = Package::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $packages = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'packages'    => PackageResource::collection($packages),
            'pagination'  => [
                'total'        => $packages->total(),
                'count'        => $packages->count(),
                'per_page'     => $packages->perPage(),
                'current_page' => $packages->currentPage(),
                'total_pages'  => $packages->lastPage(),
            ]
        ], 'Packages retrieved successfully.');
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Map request fields to database columns
        if (isset($data['duration_in_minutes'])) {
            $data['duration'] = $data['duration_in_minutes'];
            unset($data['duration_in_minutes']);
        }

        // Handle Image Uploads
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages', 'public');
            $data['image'] = asset('storage/' . $path);
        }
        
        if ($request->hasFile('desc_image')) {
            $path = $request->file('desc_image')->store('packages/desc', 'public');
            $data['desc_image'] = asset('storage/' . $path);
        }

        if ($request->hasFile('aftercare_image')) {
            $path = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $data['aftercare_image'] = asset('storage/' . $path);
        }

        $package = Package::create($data);

        return $this->success(new PackageResource($package), 'Package created successfully.', 201);
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package): JsonResponse
    {
        return $this->success(new PackageResource($package), 'Package retrieved successfully.');
    }

    /**
     * Update the specified package in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package): JsonResponse
    {
        $data = $request->validated();

        // Map request fields to database columns
        if (isset($data['duration_in_minutes'])) {
            $data['duration'] = $data['duration_in_minutes'];
            unset($data['duration_in_minutes']);
        }

        // Handle Image Uploads (with cleanup)
        if ($request->hasFile('image')) {
            $this->deleteOldFile($package->image);
            $path = $request->file('image')->store('packages', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        if ($request->hasFile('desc_image')) {
            $this->deleteOldFile($package->desc_image);
            $path = $request->file('desc_image')->store('packages/desc', 'public');
            $data['desc_image'] = asset('storage/' . $path);
        }

        if ($request->hasFile('aftercare_image')) {
            $this->deleteOldFile($package->aftercare_image);
            $path = $request->file('aftercare_image')->store('packages/aftercare', 'public');
            $data['aftercare_image'] = asset('storage/' . $path);
        }

        $package->update($data);

        return $this->success(new PackageResource($package), 'Package updated successfully.');
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(Package $package): JsonResponse
    {
        // Cleanup images
        $this->deleteOldFile($package->image);
        $this->deleteOldFile($package->desc_image);
        $this->deleteOldFile($package->aftercare_image);

        $package->delete();

        return $this->success(null, 'Package deleted successfully.');
    }

    /**
     * Helper to delete old files from storage.
     */
    protected function deleteOldFile(?string $url): void
    {
        if (!$url || !str_contains($url, '/storage/')) {
            return;
        }

        $path = str_replace(asset('storage/'), '', $url);
        Storage::disk('public')->delete($path);
    }
}
