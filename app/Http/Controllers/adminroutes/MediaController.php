<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\HomepageContent;
use App\Models\Media;
use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::orderBy('created_at', 'desc')->get();
        $total = $media->count();
        $bannersCount = $media->where('type', 'banner')->count();
        $videosCount  = $media->where('type', 'video')->count();

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
        $media = Media::where('type', 'banner')->orderBy('created_at', 'desc')->get();
        $total = Media::count();
        $bannersCount = $media->count();
        $videosCount  = Media::where('type', 'video')->count();

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
        $media = Media::where('type', 'video')->orderBy('created_at', 'desc')->get();
        $total = Media::count();
        $bannersCount = Media::where('type', 'banner')->count();
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
        $sections = HomepageContent::where('status', 'Active')->orderBy('sort_order')->get();
        return view('media.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'media_type' => 'required',
            'homepage_content_id' => 'required|exists:homepage_contents,id',
        ]);

        $filePath = null;
        if ($request->hasFile('media_file')) {
            // store() returns disk-relative path e.g. 'media/abc.jpg' — store as-is
            $filePath = $request->file('media_file')->store('media', 'public');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('media/thumbnails', 'public');
        }

        Media::create([
            'title'          => $request->title,
            'type'           => $request->media_type,
            'url'            => $filePath,
            'thumbnail'      => $thumbnailPath,
            'homepage_content_id' => $request->homepage_content_id,
            'target_page'    => $request->target_page,
            'order'          => $request->order ?? 1,
            'status'         => $request->has('status') ? 'Active' : 'Inactive',
        ]);

        return redirect()->route('media.index')->with('success', 'Media uploaded successfully!');
    }

    public function show(Media $medium)
    {
        $medium->load('homepageContent');
        return response()->json([
            'id' => $medium->id,
            'title' => $medium->title,
            'type' => $medium->type,
            'linked_section' => $medium->homepageContent ? ($medium->homepageContent->content['name'] ?? $medium->homepageContent->title ?? $medium->homepageContent->section) : '—',
            'target_page' => $medium->target_page ?? '—',
            'status' => $medium->status,
            'url' => $medium->url,
            'thumbnail' => $medium->thumbnail ?: (str_contains(strtolower($medium->type), 'video') ? 'https://images.unsplash.com/photo-1492691523567-627a5856eb0b?auto=format&fit=crop&w=400&q=80' : $medium->url),
            'created' => $medium->created_at->format('d M Y'),
        ]);
    }

    public function edit(Media $medium)
    {
        $sections = HomepageContent::where('status', 'Active')->orderBy('sort_order')->get();
        return view('media.edit', ['media' => $medium, 'sections' => $sections]);
    }

    public function update(Request $request, Media $medium)
    {
        // Keep existing value; normalize in case it was stored as a full URL previously
        $filePath = MediaPathNormalizer::normalize($medium->url);
        if ($request->hasFile('media_file')) {
            $filePath = $request->file('media_file')->store('media', 'public');
        }

        $thumbnailPath = MediaPathNormalizer::normalize($medium->thumbnail);
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('media/thumbnails', 'public');
        }

        $medium->update([
            'title'          => $request->title ?? $medium->title,
            'type'           => $request->media_type ?? $medium->type,
            'url'            => $filePath,
            'thumbnail'      => $thumbnailPath,
            'homepage_content_id' => $request->homepage_content_id ?? $medium->homepage_content_id,
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
