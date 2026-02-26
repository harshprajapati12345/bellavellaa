<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Media;
use App\Http\Resources\Api\MediaResource;
use App\Http\Requests\Api\Admin\StoreMediaRequest;
use App\Http\Requests\Api\Admin\UpdateMediaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends BaseController
{
    /**
     * Display a listing of the media.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Media::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $media = $query->orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();

        return $this->success(MediaResource::collection($media), 'Media retrieved successfully.');
    }

    /**
     * Store a newly created media in storage.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('media/thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $thumbPath);
        }

        $media = Media::create($data);

        return $this->success(new MediaResource($media), 'Media created successfully.', 201);
    }

    /**
     * Display the specified media.
     */
    public function show(Media $medium): JsonResponse
    {
        return $this->success(new MediaResource($medium), 'Media retrieved successfully.');
    }

    /**
     * Update the specified media in storage.
     */
    public function update(UpdateMediaRequest $request, Media $medium): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('media/thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $thumbPath);
        }

        $medium->update($data);

        return $this->success(new MediaResource($medium), 'Media updated successfully.');
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroy(Media $medium): JsonResponse
    {
        $medium->delete();
        return $this->success(null, 'Media deleted successfully.');
    }
}
