<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Media;
use App\Http\Resources\Api\MediaResource;
use App\Http\Requests\Api\Admin\StoreBannerRequest;
use App\Http\Requests\Api\Admin\UpdateBannerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    /**
     * Display a listing of banners.
     */
    public function index(): JsonResponse
    {
        $banners = Media::where('type', 'Banner')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(MediaResource::collection($banners), 'Banners retrieved successfully.');
    }

    /**
     * Store a newly created banner.
     */
    public function store(StoreBannerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['type'] = 'Banner';

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media/banners', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        $banner = Media::create($data);

        return $this->success(new MediaResource($banner), 'Banner created successfully.', 201);
    }

    /**
     * Display the specified banner.
     */
    public function show(int $id): JsonResponse
    {
        $banner = Media::where('type', 'Banner')->findOrFail($id);
        return $this->success(new MediaResource($banner), 'Banner retrieved successfully.');
    }

    /**
     * Update the specified banner.
     */
    public function update(UpdateBannerRequest $request, int $id): JsonResponse
    {
        $banner = Media::where('type', 'Banner')->findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media/banners', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        $banner->update($data);

        return $this->success(new MediaResource($banner), 'Banner updated successfully.');
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(int $id): JsonResponse
    {
        $banner = Media::where('type', 'Banner')->findOrFail($id);
        $banner->delete();
        return $this->success(null, 'Banner deleted successfully.');
    }
}
