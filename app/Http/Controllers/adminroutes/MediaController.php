<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::orderBy('created_at', 'desc')->get();
        $total = $media->count();
        $bannersCount = $media->where('type', 'Banner')->count();
        $videosCount  = $media->where('type', 'Video')->count();

        return view('media.index', [
            'media'   => $media,
            'total'   => $total,
            'banners' => $bannersCount,
            'videos'  => $videosCount,
            'filter'  => 'All'
        ]);
    }

    public function banners()
    {
        $media = Media::where('type', 'Banner')->orderBy('created_at', 'desc')->get();
        $total = Media::count();
        $bannersCount = $media->count();
        $videosCount  = Media::where('type', 'Video')->count();

        return view('media.index', [
            'media'   => $media,
            'total'   => $total,
            'banners' => $bannersCount,
            'videos'  => $videosCount,
            'filter'  => 'Banners'
        ]);
    }

    public function videos()
    {
        $media = Media::where('type', 'Video')->orderBy('created_at', 'desc')->get();
        $total = Media::count();
        $bannersCount = Media::where('type', 'Banner')->count();
        $videosCount  = $media->count();

        return view('media.index', [
            'media'   => $media,
            'total'   => $total,
            'banners' => $bannersCount,
            'videos'  => $videosCount,
            'filter'  => 'Videos'
        ]);
    }

    public function create()
    {
        return view('media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'media_type' => 'required',
        ]);

        $filePath = null;
        if ($request->hasFile('media_file')) {
            $stored = $request->file('media_file')->store('media', 'public');
            $filePath = asset('storage/' . $stored);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $storedThumb = $request->file('thumbnail')->store('media/thumbnails', 'public');
            $thumbnailPath = asset('storage/' . $storedThumb);
        }

        Media::create([
            'title'          => $request->title,
            'type'           => $request->media_type,
            'url'            => $filePath,
            'thumbnail'      => $thumbnailPath,
            'linked_section' => $request->linked_section,
            'target_page'    => $request->target_page,
            'order'          => $request->order ?? 1,
            'status'         => $request->has('status') ? 'Active' : 'Inactive',
        ]);

        return redirect()->route('media.index')->with('success', 'Media uploaded successfully!');
    }

    public function show(Media $medium)
    {
        return response()->json([
            'id' => $medium->id,
            'title' => $medium->title,
            'type' => $medium->type,
            'linked_section' => $medium->linked_section ?? '—',
            'target_page' => $medium->target_page ?? '—',
            'status' => $medium->status,
            'url' => $medium->url,
            'thumbnail' => $medium->thumbnail ?: (str_contains(strtolower($medium->type), 'video') ? 'https://images.unsplash.com/photo-1492691523567-627a5856eb0b?auto=format&fit=crop&w=400&q=80' : $medium->url),
            'created' => $medium->created_at->format('d M Y'),
        ]);
    }

    public function edit(Media $medium)
    {
        return view('media.edit', ['media' => $medium]);
    }

    public function update(Request $request, Media $medium)
    {
        $filePath = $medium->url;
        if ($request->hasFile('media_file')) {
            $stored = $request->file('media_file')->store('media', 'public');
            $filePath = asset('storage/' . $stored);
        }

        $thumbnailPath = $medium->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $storedThumb = $request->file('thumbnail')->store('media/thumbnails', 'public');
            $thumbnailPath = asset('storage/' . $storedThumb);
        }

        $medium->update([
            'title'          => $request->title ?? $medium->title,
            'type'           => $request->media_type ?? $medium->type,
            'url'            => $filePath,
            'thumbnail'      => $thumbnailPath,
            'linked_section' => $request->linked_section ?? $medium->linked_section,
            'target_page'    => $request->target_page ?? $medium->target_page,
            'order'          => $request->order ?? $medium->order,
            'status'         => $request->has('status') ? 'Active' : 'Inactive',
        ]);

        return redirect()->route('media.index')->with('success', 'Media updated successfully!');
    }

    public function destroy(Media $medium)
    {
        $medium->delete();
        return redirect()->route('media.index')->with('success', 'Media deleted.');
    }
}
