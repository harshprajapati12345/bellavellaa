<?php

namespace App\Http\Controllers\adminroutes;

use App\Http\Requests\Admin\StoreHierarchyBannerRequest;
use App\Http\Requests\Admin\UpdateHierarchyBannerRequest;
use App\Models\Category;
use App\Models\HierarchyBanner;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HierarchyBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = HierarchyBanner::query()->with('target');

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->string('target_type')->toString());
        }

        if ($request->filled('placement_type')) {
            $query->where('placement_type', $request->string('placement_type')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('subtitle', 'like', '%' . $search . '%');
            });
        }

        return view('hierarchy_banners.index', [
            'banners' => $query->orderBy('sort_order')->orderByDesc('id')->paginate(12)->withQueryString(),
            ...$this->formOptions(),
        ]);
    }

    public function create()
    {
        return view('hierarchy_banners.create', $this->formOptions());
    }

    public function store(StoreHierarchyBannerRequest $request)
    {
        $data = $request->validated();
        $data['media_path'] = $request->file('media_file')->store('media/hierarchy-banners', 'public');
        $data['thumbnail_path'] = $request->hasFile('thumbnail_file')
            ? $request->file('thumbnail_file')->store('media/hierarchy-banners', 'public')
            : null;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($data['media_type'] !== 'video') {
            $data['thumbnail_path'] = null;
        }

        unset($data['media_file'], $data['thumbnail_file']);

        HierarchyBanner::create($data);

        return redirect()->route('hierarchy-banners.index')
            ->with('success', 'Service flow banner created successfully.');
    }

    public function edit(HierarchyBanner $hierarchyBanner)
    {
        return view('hierarchy_banners.edit', [
            'hierarchyBanner' => $hierarchyBanner,
            ...$this->formOptions(),
        ]);
    }

    public function update(
        UpdateHierarchyBannerRequest $request,
        HierarchyBanner $hierarchyBanner
    ) {
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            if ($hierarchyBanner->media_path) {
                Storage::disk('public')->delete($hierarchyBanner->media_path);
            }

            $data['media_path'] = $request->file('media_file')->store('media/hierarchy-banners', 'public');
        }

        if ($request->hasFile('thumbnail_file')) {
            if ($hierarchyBanner->thumbnail_path) {
                Storage::disk('public')->delete($hierarchyBanner->thumbnail_path);
            }

            $data['thumbnail_path'] = $request->file('thumbnail_file')->store('media/hierarchy-banners', 'public');
        } elseif (($data['media_type'] ?? $hierarchyBanner->media_type) !== 'video') {
            if ($hierarchyBanner->thumbnail_path) {
                Storage::disk('public')->delete($hierarchyBanner->thumbnail_path);
            }

            $data['thumbnail_path'] = null;
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['media_file'], $data['thumbnail_file']);

        $hierarchyBanner->update($data);

        return redirect()->route('hierarchy-banners.index')
            ->with('success', 'Service flow banner updated successfully.');
    }

    public function destroy(HierarchyBanner $hierarchyBanner)
    {
        if ($hierarchyBanner->media_path) {
            Storage::disk('public')->delete($hierarchyBanner->media_path);
        }

        if ($hierarchyBanner->thumbnail_path) {
            Storage::disk('public')->delete($hierarchyBanner->thumbnail_path);
        }

        $hierarchyBanner->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('hierarchy-banners.index')
            ->with('success', 'Service flow banner deleted successfully.');
    }

    public function toggleStatus(Request $request, HierarchyBanner $hierarchyBanner)
    {
        $validated = $request->validate([
            'status' => 'required|in:Active,Inactive',
        ]);

        $hierarchyBanner->update(['status' => $validated['status']]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Banner status updated successfully.');
    }

    protected function formOptions(): array
    {
        return [
            'categories' => Category::where('status', 'Active')->orderBy('name')->get(['id', 'name']),
            'serviceGroups' => ServiceGroup::with('category:id,name')
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'category_id', 'name']),
            'serviceTypes' => ServiceType::with('serviceGroup.category:id,name')
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'service_group_id', 'name']),
            'services' => Service::with('serviceType.serviceGroup.category:id,name')
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'service_type_id', 'name']),
            'variants' => ServiceVariant::with('service:id,name')
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'service_id', 'name']),
        ];
    }
}
