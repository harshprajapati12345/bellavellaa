@extends('layouts.app')
@php $pageTitle = 'Category Banners'; @endphp

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Category Banners</h2>
            <p class="text-sm text-gray-400 mt-0.5">Manage slider and promo banners for categories</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('category-banners.create') }}"
                class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Banner
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
        <form method="GET" action="{{ route('category-banners.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Category</label>
                <select name="category_id" class="form-input" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Type</label>
                <select name="banner_type" class="form-input" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="slider" {{ request('banner_type') == 'slider' ? 'selected' : '' }}>Slider</option>
                    <option value="promo" {{ request('banner_type') == 'promo' ? 'selected' : '' }}>Promo</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2 block">Status</label>
                <select name="status" class="form-input" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('category-banners.index') }}" class="btn btn-secondary w-full">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Banner</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Order</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 group transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                                    <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-cover">
                                </div>
                                <div class="max-w-[200px]">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $banner->title ?? 'No Title' }}</p>
                                    @if($banner->subtitle)
                                        <p class="text-[11px] text-gray-400 truncate">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                            {{ $banner->category->name }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $banner->banner_type === 'slider' ? 'bg-black text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $banner->banner_type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            {{ $banner->sort_order }}
                        </td>
                        <td class="px-5 py-4">
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $banner->status === 'Active' ? 'checked' : '' }}
                                    onchange="toggleBannerStatus({{ $banner->id }}, this)">
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('category-banners.edit', $banner->id) }}" title="Edit"
                                    class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <button type="button" title="Delete"
                                    onclick="deleteBanner({{ $banner->id }}, '{{ addslashes($banner->title ?? 'this banner') }}')"
                                    class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                <i data-lucide="layout" class="w-7 h-7 text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No banners found</p>
                            <p class="text-gray-400 text-sm mt-1">Try adding a new banner or adjusting your filters.</p>
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
        fetch(`{{ url('category-banners') }}/${id}/toggle-status`, {
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
                Swal.fire({ 
                    title: 'Updated!', 
                    text: `Banner is now ${newStatus}.`, 
                    icon: 'success', 
                    timer: 1500, 
                    showConfirmButton: false 
                });
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
            if (result.isConfirmed) {
                fetch(`{{ url('category-banners') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
