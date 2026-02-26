<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\HomepageContent;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        $sections = HomepageContent::orderBy('sort_order', 'asc')->get();
        return view('homepage.index', compact('sections'));
    }

    public function create()
    {
        return view('homepage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content_type' => 'required|string'
        ]);

        $imagePath = null;
        if ($request->hasFile('section_image')) {
            $stored = $request->file('section_image')->store('homepage', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $status = $request->has('status') ? 'Active' : 'Inactive';
        // If "Save as Draft" button was clicked, we might want to force Inactive
        // But let's stick to the toggle as the primary source of truth if both exist.

        HomepageContent::create([
            'section'    => $request->key ?? str($request->name)->slug(),
            'title'      => $request->title,
            'image'      => $imagePath,
            'status'     => $status,
            'sort_order' => $request->order ?? (HomepageContent::max('sort_order') + 1),
            'content'    => [
                'name'         => $request->name,
                'content_type' => $request->content_type,
                'data_source'  => $request->data_source,
                'subtitle'     => $request->subtitle,
                'description'  => $request->description,
                'btn_text'     => $request->btn_text,
                'btn_link'     => $request->btn_link,
            ],
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
            'name' => 'required|string|max:255',
            'content_type' => 'required|string'
        ]);

        $imagePath = $homepage->image;
        if ($request->hasFile('section_image')) {
            $stored = $request->file('section_image')->store('homepage', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $homepage->update([
            'section'    => $request->key ?? $homepage->section,
            'title'      => $request->title,
            'image'      => $imagePath,
            'status'     => $request->has('status') ? 'Active' : 'Inactive',
            'sort_order' => $request->order ?? $homepage->sort_order,
            'content'    => [
                'name'         => $request->name,
                'content_type' => $request->content_type,
                'data_source'  => $request->data_source,
                'subtitle'     => $request->subtitle,
                'description'  => $request->description,
                'btn_text'     => $request->btn_text,
                'btn_link'     => $request->btn_link,
            ],
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
