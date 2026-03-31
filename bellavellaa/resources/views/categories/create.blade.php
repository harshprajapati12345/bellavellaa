@extends('layouts.app')
@php $pageTitle = 'Add Category'; @endphp

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('categories.index') }}"
        class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
      </a>
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Category</h2>
        <p class="text-sm text-gray-400 mt-0.5">Create a new service category</p>
      </div>
    </div>

    @if(session('success'))
      <div
        class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium shadow-sm">
        <i data-lucide="check-circle" class="w-5 h-5"></i> Category created successfully!</div>
    @endif
    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li><i data-lucide="alert-circle"
        class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data"
      class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
      @csrf
      <div class="p-8 lg:p-10 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> General Information
            </h3>
            <div>
              <label class="form-label font-semibold">Category Name *</label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Bridal Glam" class="form-input"
                required>
            </div>

            {{-- TYPE: required, drives Flutter behavior --}}
            <div>
              <label class="form-label font-semibold">Category Type <span class="text-red-400">*</span></label>
              <p class="text-xs text-gray-400 mb-3">Controls what this category shows in the app</p>
              <div class="flex gap-4">
                <label class="flex items-center gap-2.5 cursor-pointer group">
                  <input type="radio" name="type" value="services" {{ old('type', 'services') === 'services' ? 'checked' : '' }}
                    class="w-4 h-4 accent-black cursor-pointer" required>
                  <span class="text-sm font-medium text-gray-700 group-hover:text-black">
                    Services <span class="text-xs text-gray-400">(Salon, Spa, Hair Studio)</span>
                  </span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer group">
                  <input type="radio" name="type" value="packages" {{ old('type') === 'packages' ? 'checked' : '' }}
                    class="w-4 h-4 accent-black cursor-pointer">
                  <span class="text-sm font-medium text-gray-700 group-hover:text-black">
                    Packages <span class="text-xs text-gray-400">(Bride)</span>
                  </span>
                </label>
              </div>
            </div>

            <div>
              <label class="form-label font-semibold">Description</label>
              <textarea name="description" rows="4" placeholder="Tell us about this category…"
                class="form-input resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
              <label class="form-label font-semibold">Sort Order</label>
              <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-input" placeholder="0">
              <p class="text-xs text-gray-400 mt-1">Lower number = shown first in app (0 = auto)</p>
            </div>
          </div>
          <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Visibility
            </h3>
            <div>
              <label class="form-label font-semibold">Cover Image</label>
              <div class="flex gap-4 items-start">
                <label
                  class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1 pb-2">
                  <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                  </div>
                  <p class="text-sm font-medium text-gray-600">Click to upload</p>
                  <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 10MB</p>
                  <input type="file" name="image" class="hidden" onchange="previewImage(this)" accept="image/*">
                </label>
                <div id="preview-container" class="hidden w-40 h-40 relative group">
                  <img id="img-preview" class="w-full h-full object-cover rounded-[2rem] border border-gray-100" src=""
                    alt="">
                  <div
                    class="absolute inset-0 bg-black/40 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
              <div>
                <p class="text-sm font-semibold text-gray-900">Active Status</p>
                <p class="text-xs text-gray-400 mt-0.5">Visible on your booking platform</p>
              </div>
              <label class="toggle-switch"><input type="checkbox" name="status" checked><span
                  class="toggle-slider"></span></label>
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
              <div>
                <p class="text-sm font-semibold text-gray-900">Show on Homepage</p>
                <p class="text-xs text-gray-400 mt-0.5">Feature this category in the app home screen</p>
              </div>
              <label class="toggle-switch"><input type="checkbox" name="featured" checked><span
                  class="toggle-slider"></span></label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Discard Changes</a>
        <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
          <i data-lucide="check" class="w-4 h-4"></i> Create Category
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('img-preview').src = e.target.result;
          document.getElementById('preview-container').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endpush