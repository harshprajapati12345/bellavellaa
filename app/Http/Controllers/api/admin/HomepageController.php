<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\HomepageContent;
use App\Http\Resources\Api\HomepageResource;
use App\Http\Requests\Api\Admin\StoreHomepageRequest;
use App\Http\Requests\Api\Admin\UpdateHomepageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomepageController extends BaseController
{
    /**
     * Display a listing of homepage sections.
     */
    public function index(): JsonResponse
    {
        $sections = HomepageContent::orderBy('sort_order', 'asc')->get();
        return $this->success(HomepageResource::collection($sections), 'Homepage sections retrieved.');
    }

    /**
     * Store a newly created section.
     */
    public function store(StoreHomepageRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('section_image')) {
            $path = $request->file('section_image')->store('homepage', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $section = HomepageContent::create($data);

        return $this->success(new HomepageResource($section), 'Section created successfully.', 201);
    }

    /**
     * Display the specified section.
     */
    public function show(HomepageContent $homepage): JsonResponse
    {
        return $this->success(new HomepageResource($homepage), 'Section retrieved successfully.');
    }

    /**
     * Update the specified section.
     */
    public function update(UpdateHomepageRequest $request, HomepageContent $homepage): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('section_image')) {
            $path = $request->file('section_image')->store('homepage', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $homepage->update($data);

        return $this->success(new HomepageResource($homepage), 'Section updated successfully.');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(HomepageContent $homepage): JsonResponse
    {
        $homepage->delete();
        return $this->success(null, 'Section deleted successfully.');
    }

    /**
     * Bulk reorder sections.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:homepage_contents,id',
            'order.*.position' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            HomepageContent::where('id', $item['id'])->update(['sort_order' => $item['position']]);
        }

        return $this->success(null, 'Sections reordered successfully.');
    }
}
