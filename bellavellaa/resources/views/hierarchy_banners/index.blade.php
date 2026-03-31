@extends('layouts.app')
@php $pageTitle = 'Service Flow Banners'; @endphp

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Service Flow Banners</h2>
            <p class="text-sm text-gray-400 mt-0.5">Manage page header, promo, and popup banners for hierarchy entities.</p>
        </div>
        <a href="{{ route('hierarchy-banners.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Banner
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
        <form method="GET" action="{{ route('hierarchy-banners.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Target</label>
                <select name="target_type" class="form-input" onchange="this.form.submit()">
                    <option value="">All Targets</option>
                    <option value="category" {{ request('target_type') === 'category' ? 'selected' : '' }}>Category</option>
                    <option value="service_group" {{ request('target_type') === 'service_group' ? 'selected' : '' }}>Service Group</option>
                    <option value="service_type" {{ request('target_type') === 'service_type' ? 'selected' : '' }}>Service Type</option>
                    <option value="service" {{ request('target_type') === 'service' ? 'selected' : '' }}>Service</option>
                    <option value="variant" {{ request('target_type') === 'variant' ? 'selected' : '' }}>Variant</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Placement</label>
                <select name="placement_type" class="form-input" onchange="this.form.submit()">
                    <option value="">All Placements</option>
                    <option value="page_header" {{ request('placement_type') === 'page_header' ? 'selected' : '' }}>Page Header</option>
                    <option value="promo_banner" {{ request('placement_type') === 'promo_banner' ? 'selected' : '' }}>Promo Banner</option>
                    <option value="popup_banner" {{ request('placement_type') === 'popup_banner' ? 'selected' : '' }}>Popup Banner</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Status</label>
                <select name="status" class="form-input" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Banner title">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary w-full">Apply</button>
                <a href="{{ route('hierarchy-banners.index') }}" class="btn btn-secondary w-full">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Banner</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Placement</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Target</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Media</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Order</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    @php
                        $previewUrl = $banner->thumbnail_path ? asset('storage/' . $banner->thumbnail_path) : asset('storage/' . $banner->media_path);
                        $targetName = $banner->target?->name ?? ('#' . $banner->target_id);
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 group transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200 bg-gray-100">
                                    @if($banner->media_type === 'video')
                                        <div class="w-full h-full relative">
                                            <img src="{{ $previewUrl }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/35 flex items-center justify-center">
                                                <i data-lucide="play-circle" class="w-5 h-5 text-white"></i>
                                            </div>
                                        </div>
                                    @else
                                        <img src="{{ $previewUrl }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="max-w-[220px]">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $banner->title ?: 'Untitled banner' }}</p>
                                    @if($banner->subtitle)
                                        <p class="text-[11px] text-gray-400 truncate">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ str_replace('_', ' ', $banner->placement_type) }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $targetName }}</p>
                            <p class="text-[11px] uppercase tracking-wide text-gray-400">{{ str_replace('_', ' ', $banner->target_type) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $banner->media_type === 'video' ? 'bg-black text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $banner->media_type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $banner->sort_order }}</td>
                        <td class="px-5 py-4">
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $banner->status === 'Active' ? 'checked' : '' }}
                                    onchange="toggleBannerStatus({{ $banner->id }}, this)">
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('hierarchy-banners.edit', $banner->id) }}"
                                    class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <button type="button"
                                    onclick="deleteBanner({{ $banner->id }}, '{{ addslashes($banner->title ?: 'this banner') }}')"
                                    class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                <i data-lucide="gallery-horizontal" class="w-7 h-7 text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No service flow banners found</p>
                            <p class="text-gray-400 text-sm mt-1">Create a placement banner for a category, group, type, service, or variant.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $banners->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleBannerStatus(id, checkbox) {
        const newStatus = checkbox.checked ? 'Active' : 'Inactive';
        fetch(`{{ url('hierarchy-banners') }}/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        }).then(res => res.json())
          .then(data => {
            if (data.success) {
                Swal.fire({ title: 'Updated!', text: `Banner is now ${newStatus}.`, icon: 'success', timer: 1500, showConfirmButton: false });
            }
          })
          .catch(() => {
            checkbox.checked = !checkbox.checked;
            Swal.fire('Error', 'Failed to update status.', 'error');
          });
    }

    function deleteBanner(id, title) {
        Swal.fire({
            title: 'Delete Banner?',
            text: `Are you sure you want to remove "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            fetch(`{{ url('hierarchy-banners') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ title: 'Deleted!', icon: 'success', timer: 1500, showConfirmButton: false })
                        .then(() => window.location.reload());
                }
            });
        });
    }
</script>
@endpush
