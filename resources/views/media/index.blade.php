@extends('layouts.app')
@php $pageTitle = 'Media Manager'; @endphp

@section('content')
@php
$media = $media ?? collect();
$totalMedia = $total ?? $media->count();
$totalImages = $banners ?? $media->where('type', 'Banner')->count();
$totalVideos = $videos ?? $media->where('type', 'Video')->count();
$totalActive = $media->where('status', 'Active')->count();
$currentFilter = $filter ?? 'All';
@endphp

    <div class="flex flex-col gap-6">

      <!-- Stats Row -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Media</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalMedia }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Images</p>
          <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalImages }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Videos</p>
          <p class="text-2xl font-bold text-purple-600 mt-1">{{ $totalVideos }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Active</p>
          <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalActive }}</p>
        </div>
      </div>

      <!-- Filters & Actions -->
      <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
            <div class="relative w-full sm:w-60">
              <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
              <input type="text" id="media-search" placeholder="Search media..." oninput="filterMedia()"
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
            </div>
            <select id="type-filter" onchange="filterMedia()" class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 bg-white">
              <option value="">All Types</option>
              <option>Image</option>
              <option>Video</option>
            </select>
            <select id="section-filter" onchange="filterMedia()" class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 bg-white">
              <option value="">All Sections</option>
              <option>Hero Banner</option>
              <option>Gallery</option>
              <option>About</option>
              <option>Services</option>
            </select>
          </div>
          <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('media.banners.index') }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
              <i data-lucide="image" class="w-4 h-4"></i>Banners
            </a>
            <a href="{{ route('media.videos.index') }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
              <i data-lucide="video" class="w-4 h-4"></i>Videos
            </a>
            <a href="{{ route('media.create') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-black text-white px-6 py-2.5 rounded-xl hover:bg-gray-800 transition-all font-medium text-sm shadow-sm">
              <i data-lucide="plus" class="w-4 h-4"></i>Add Media
            </a>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left" id="media-table">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">ID</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Preview</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Title</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Linked Section</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($media as $m)
              @php
                $typeBg = ($m->type ?? 'Banner') === 'Banner' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600';
                $typeIcon = ($m->type ?? 'Banner') === 'Banner' ? 'image' : 'video';
              @endphp
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $m->id }}"
                  data-title="{{ $m->title ?? '' }}"
                  data-search-title="{{ strtolower($m->title ?? '') }}"
                  data-type="{{ $m->type ?? '' }}"
                  data-section="{{ $m->section ?? $m->linked_section ?? '—' }}"
                  data-order="{{ $m->order ?? $m->sort_order ?? '—' }}"
                  data-status="{{ ($m->status ?? false) ? 'Active' : 'Inactive' }}"
                  data-preview="{{ $m->file_path ?? $m->url ?? '' }}"
                  data-description="{{ $m->description ?? 'No description available.' }}"
                  data-created="{{ \Carbon\Carbon::parse($m->created_at)->format('d M Y') }}">
                <td class="px-6 py-4 text-sm text-gray-400 font-medium">#{{ $m->id }}</td>
                <td class="px-4 py-4">
                  <div class="media-preview flex items-center justify-center bg-gray-100">
                    @if(!empty($m->file_path) || !empty($m->url))
                    <img src="{{ $m->file_path ?? $m->url }}" class="w-full h-full object-cover rounded-xl" alt="">
                    @else
                    <i data-lucide="{{ $typeIcon }}" class="w-5 h-5 text-gray-400"></i>
                    @endif
                  </div>
                </td>
                <td class="px-4 py-4"><span class="text-sm font-semibold text-gray-900">{{ $m->title }}</span></td>
                <td class="px-4 py-4"><span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ $typeBg }}">{{ $m->type }}</span></td>
                <td class="px-4 py-4 text-sm text-gray-500">{{ $m->section ?? $m->linked_section ?? '—' }}</td>
                <td class="px-4 py-4 text-sm text-gray-400 text-center">{{ $m->order ?? $m->sort_order ?? '—' }}</td>
                <td class="px-4 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" {{ ($m->status ?? false) ? 'checked' : '' }} onchange="toggleStatus({{ $m->id }}, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td class="px-4 py-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button class="view-btn w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" data-id="{{ $m->id }}" data-type="media" title="View Details">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <a href="{{ route('media.edit', $m->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" title="Edit">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    <button onclick="deleteMedia({{ $m->id }}, '{{ addslashes($m->title ?? '') }}')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="8" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i></div>
                  <p class="text-sm text-gray-400 font-medium">No media found</p>
                </div>
              </td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-8 py-5 border-t border-gray-100 gap-3">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1"></div>
        </div>
      </div>

    </div>

@endsection

@push('styles')
<style>
  .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
</style>
@endpush

@push('scripts')
<script>
  /* Client-side filter */
  function deleteMedia(id, name) {
    Swal.fire({
      title: 'Delete Media?',
      text: `"${name}" will be permanently removed.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete it'
    }).then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('media.destroy', '') }}/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endpush
