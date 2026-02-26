<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Media;
use App\Http\Resources\Api\MediaResource;
use App\Http\Requests\Api\Admin\StoreVideoRequest;
use App\Http\Requests\Api\Admin\UpdateVideoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends BaseController
{
    /**
     * Display a listing of videos.
     */
    public function index(): JsonResponse
    {
        $videos = Media::where('type', 'Video')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(MediaResource::collection($videos), 'Videos retrieved successfully.');
    }

    /**
     * Store a newly created video.
     */
    public function store(StoreVideoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['type'] = 'Video';

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media/videos', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('media/videos/thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $thumbPath);
        }

        $video = Media::create($data);

        return $this->success(new MediaResource($video), 'Video created successfully.', 201);
    }

    /**
     * Display the specified video.
     */
    public function show(int $id): JsonResponse
    {
        $video = Media::where('type', 'Video')->findOrFail($id);
        return $this->success(new MediaResource($video), 'Video retrieved successfully.');
    }

    /**
     * Update the specified video.
     */
    public function update(UpdateVideoRequest $request, int $id): JsonResponse
    {
        $video = Media::where('type', 'Video')->findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('media/videos', 'public');
            $data['url'] = asset('storage/' . $path);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('media/videos/thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $thumbPath);
        }

        $video->update($data);

        return $this->success(new MediaResource($video), 'Video updated successfully.');
    }

    /**
     * Remove the specified video.
     */
    public function destroy(int $id): JsonResponse
    {
        $video = Media::where('type', 'Video')->findOrFail($id);
        $video->delete();
        return $this->success(null, 'Video deleted successfully.');
    }
}
