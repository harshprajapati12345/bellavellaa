<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Service;
use App\Http\Resources\Api\ServiceResource;
use App\Http\Requests\Api\Admin\StoreServiceRequest;
use App\Http\Requests\Api\Admin\UpdateServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the services.
     */
    public function index(): JsonResponse
    {
        $query = Service::query();

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

        $services = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'services'    => ServiceResource::collection($services),
            'pagination'  => [
                'total'        => $services->total(),
                'count'        => $services->count(),
                'per_page'     => $services->perPage(),
                'current_page' => $services->currentPage(),
                'total_pages'  => $services->lastPage(),
            ]
        ], 'Services retrieved successfully.');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Map request fields to database columns
        if (isset($data['duration_in_minutes'])) {
            $data['duration'] = $data['duration_in_minutes'];
            unset($data['duration_in_minutes']);
        }

        // Handle Image Uploads
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $service = Service::create($data);

        return $this->success(new ServiceResource($service), 'Service created successfully.', 201);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): JsonResponse
    {
        return $this->success(new ServiceResource($service), 'Service retrieved successfully.');
    }

    /**
     * Update the specified service in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $data = $request->validated();

        // Map request fields to database columns
        if (isset($data['duration_in_minutes'])) {
            $data['duration'] = $data['duration_in_minutes'];
            unset($data['duration_in_minutes']);
        }

        // Handle Image Uploads (with cleanup)
        if ($request->hasFile('image')) {
            $this->deleteOldFile($service->image);
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $service->update($data);

        return $this->success(new ServiceResource($service), 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        // Cleanup image
        $this->deleteOldFile($service->image);

        $service->delete();

        return $this->success(null, 'Service deleted successfully.');
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
