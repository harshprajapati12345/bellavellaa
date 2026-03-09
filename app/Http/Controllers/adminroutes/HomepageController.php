<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\HomepageContent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomepageController extends Controller
{
    public function index()
    {
        $sections = HomepageContent::orderBy('sort_order', 'asc')->get();
        return view('homepage.index', compact('sections'));
    }

    public function create()
    {
        $usedSections = HomepageContent::pluck('section')->toArray();
        return view('homepage.create', compact('usedSections'));
    }

    // All valid Flutter client homepage section types
    public const SECTION_TYPES = [
        'hero_banner'        => 'Hero Banner',
        'category_carousel'  => 'Category Carousel',
        'service_carousel'   => 'Service Carousel',
        'service_grid'       => 'Service Grid',
        'video_stories'      => 'Video Stories',
        'image_banner'       => 'Image Banner',
        'active_booking'     => 'Active Booking',
        'testimonials'       => 'Testimonials',
        'trending_packages'  => 'Trending Packages',
        'download_app'       => 'Download App',
    ];

    public function store(Request $request)
    {
        $request->validate([
            'section_type' => [
                'required',
                Rule::in(array_keys(self::SECTION_TYPES)),
                Rule::unique('homepage_contents', 'section'),
            ],
            'title'        => 'required|string|max:255',
            'media_type'   => 'required|string|in:banner,video',
        ]);

        $sectionKey = $request->section_type;
        $label      = self::SECTION_TYPES[$sectionKey];

        $imagePath = null;
        if ($request->hasFile('section_image')) {
            $stored    = $request->file('section_image')->store('homepage', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        HomepageContent::create([
            'section'      => $sectionKey,
            'name'         => $label,
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'content_type' => 'dynamic',
            'media_type'   => $request->media_type,
            'description'  => $request->description,
            'image'        => $imagePath,
            'status'       => $request->has('status') ? 'Active' : 'Inactive',
            'sort_order'   => HomepageContent::max('sort_order') + 1,
            'content'      => [],
        ]);

        return redirect()->route('homepage.index')->with('success', 'Section created successfully!');
    }

    public function show(HomepageContent $homepage)
    {
        return redirect()->route('homepage.index');
    }

    public function edit(HomepageContent $homepage)
    {
        return view('homepage.edit', compact('homepage'));
    }

    public function update(Request $request, HomepageContent $homepage)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'media_type' => 'required|string|in:banner,video',
        ]);

        $imagePath = $homepage->image;
        if ($request->hasFile('section_image')) {
            $stored    = $request->file('section_image')->store('homepage', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $homepage->update([
            // 'section' and 'name' are locked — cannot change section type after creation
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'media_type'   => $request->media_type,
            'description'  => $request->description,
            'image'        => $imagePath,
            'status'       => $request->has('status') ? 'Active' : 'Inactive',
            // Preserve sort_order, content, section, name
        ]);

        return redirect()->route('homepage.index')->with('success', 'Section updated successfully!');
    }

    public function destroy(HomepageContent $homepage)
    {
        $homepage->delete();
        return redirect()->route('homepage.index')->with('success', 'Content deleted.');
    }

    public function toggleStatus(Request $request, HomepageContent $homepage)
    {
        $homepage->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $item) {
            HomepageContent::where('id', $item['id'])->update(['sort_order' => $item['position']]);
        }
        return response()->json(['success' => true]);
    }
}
