@extends('layouts.app')
@php $pageTitle = 'Add Service Group'; @endphp

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('service-groups.index') }}"
        class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
      </a>
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Service Group</h2>
        <p class="text-sm text-gray-400 mt-0.5">Create a tier under a service-type category (e.g. Luxe under Salon)</p>
      </div>
    </div>

    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('service-groups.store') }}" enctype="multipart/form-data"
      class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
      @csrf
      <div class="p-8 lg:p-10 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

          {{-- Left: Core fields --}}
          <div class="space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Group Details
            </h3>

            <div>
              <label class="form-label font-semibold">Parent Category <span class="text-red-400">*</span></label>
              <p class="text-xs text-gray-400 mb-2">Only services-type categories shown</p>
              <select name="category_id" class="form-input cursor-pointer" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="form-label font-semibold">Group Name <span class="text-red-400">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Luxe" class="form-input" required>
              <p class="text-xs text-gray-400 mt-1">Slug is auto-generated from category + name</p>
            </div>

            <div>
              <label class="form-label font-semibold">Tag Label <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
              <input type="text" name="tag_label" value="{{ old('tag_label') }}" placeholder="e.g. Premium, Bestseller" class="form-input">
            </div>

            <div>
              <label class="form-label font-semibold">Description <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
              <textarea name="description" rows="3" placeholder="Describe this service group…" class="form-input resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
              <label class="form-label font-semibold">Sort Order</label>
              <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-input" placeholder="0">
              <p class="text-xs text-gray-400 mt-1">Lower = shown first</p>
            </div>
          </div>

          {{-- Right: Image + Status --}}
          <div class="space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Visibility
            </h3>

            <div>
              <label class="form-label font-semibold">Group Image <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
              <div class="flex gap-4 items-start">
                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1">
                  <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                  </div>
                  <p class="text-sm font-medium text-gray-600">Click to upload</p>
                  <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                  <input type="file" name="image" class="hidden" onchange="previewImg(this)" accept="image/*">
                </label>
                <div id="preview-container" class="hidden w-40 h-40 relative group">
                  <img id="img-preview" class="w-full h-full object-cover rounded-[2rem] border border-gray-100" src="" alt="">
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
              <div>
                <p class="text-sm font-semibold text-gray-900">Active Status</p>
                <p class="text-xs text-gray-400 mt-0.5">Visible on booking platform</p>
              </div>
              <label class="toggle-switch"><input type="checkbox" name="status" checked><span class="toggle-slider"></span></label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
        <a href="{{ route('service-groups.index') }}" class="btn btn-secondary">Discard</a>
        <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
          <i data-lucide="check" class="w-4 h-4"></i> Create Group
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    function previewImg(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
          document.getElementById('img-preview').src = e.target.result;
          document.getElementById('preview-container').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endpush
