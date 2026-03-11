<?php

namespace App\Http\Controllers\adminroutes;

use App\Http\Requests\Admin\StoreServiceTypeRequest;
use App\Http\Requests\Admin\UpdateServiceTypeRequest;
use App\Models\Category;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Support\MediaPathNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'services')->where('status', 'Active')->orderBy('sort_order')->get();
        $groups = ServiceGroup::with('category')->where('status', 'Active')->orderBy('sort_order')->get();

        $types = ServiceType::with(['serviceGroup.category'])
            ->withCount('services')
            ->when($request->filled('category_id'), fn ($query) => $query->whereHas('serviceGroup', fn ($groupQuery) => $groupQuery->where('category_id', $request->integer('category_id'))))
            ->when($request->filled('service_group_id'), fn ($query) => $query->where('service_group_id', $request->integer('service_group_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('service-types.index', compact('types', 'categories', 'groups'));
    }

    public function create()
    {
        $categories = Category::where('type', 'services')->where('status', 'Active')->orderBy('sort_order')->get();
        $groups = ServiceGroup::with('category')->where('status', 'Active')->orderBy('sort_order')->get();

        return view('service-types.create', compact('categories', 'groups'));
    }

    public function store(StoreServiceTypeRequest $request)
    {
        $data = $request->validated();
        $group = ServiceGroup::where('status', 'Active')->findOrFail($data['service_group_id']);

        $data['slug'] = $this->uniqueSlug(
            $group->id,
            $data['slug'] ?? Str::slug($data['name'])
        );

        if ($request->hasFile('image')) {
            $data['image'] = MediaPathNormalizer::normalize(
                $request->file('image')->store('service-types', 'public')
            );
        }

        $data['status'] = $data['status'] ?? 'Active';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        ServiceType::create($data);

        return redirect()->route('service-types.index')->with('success', 'Service type created successfully.');
    }

    public function edit(ServiceType $serviceType)
    {
        $categories = Category::where('type', 'services')->where('status', 'Active')->orderBy('sort_order')->get();
        $groups = ServiceGroup::with('category')->where('status', 'Active')->orderBy('sort_order')->get();

        return view('service-types.edit', compact('serviceType', 'categories', 'groups'));
    }

    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType)
    {
        $data = $request->validated();
        $group = ServiceGroup::where('status', 'Active')->findOrFail($data['service_group_id']);

        $data['slug'] = $this->uniqueSlug(
            $group->id,
            $data['slug'] ?? Str::slug($data['name']),
            $serviceType->id
        );

        if ($request->hasFile('image')) {
            if ($serviceType->image) {
                Storage::disk('public')->delete($serviceType->image);
            }

            $data['image'] = MediaPathNormalizer::normalize(
                $request->file('image')->store('service-types', 'public')
            );
        }

        $serviceType->update($data);

        return redirect()->route('service-types.index')->with('success', 'Service type updated successfully.');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->services()->exists()) {
            return redirect()->route('service-types.index')
                ->withErrors(['delete' => 'Delete or move child services before removing this service type.']);
        }

        $serviceType->delete();

        return redirect()->route('service-types.index')->with('success', 'Service type deleted successfully.');
    }

    protected function uniqueSlug(int $serviceGroupId, string $slug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($slug);
        $base = $slug;
        $counter = 1;

        while (
            ServiceType::where('service_group_id', $serviceGroupId)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
