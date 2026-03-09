@extends('layouts.app')
@php $pageTitle = 'Edit Service Group'; @endphp

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('service-groups.index') }}"
        class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
      </a>
      <div class="flex-1">
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Service Group</h2>
        <p class="text-sm text-gray-400 mt-0.5">Editing: <span class="text-black font-medium">{{ $serviceGroup->name }}</span></p>
      </div>
    </div>

    @if(session('success'))
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium">
        <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-3 rounded-2xl text-xs font-medium">
      <strong>Slug:</strong> <code class="font-mono">{{ $serviceGroup->slug }}</code>
      — auto-regenerated if name or category changes.
    </div>

    <form method="POST" action="{{ route('service-groups.update', $serviceGroup->id) }}" enctype="multipart/form-data"
      class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
      @csrf @method('PUT')
      <div class="p-8 lg:p-10 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

          {{-- Left --}}
          <div class="space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Group Details
            </h3>

            <div>
              <label class="form-label font-semibold">Parent Category <span class="text-red-400">*</span></label>
              <select name="category_id" class="form-input cursor-pointer" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id', $serviceGroup->category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="form-label font-semibold">Group Name <span class="text-red-400">*</span></label>
              <input type="text" name="name" value="{{ old('name', $serviceGroup->name) }}" class="form-input" required>
            </div>

            <div>
              <label class="form-label font-semibold">Tag Label <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
              <input type="text" name="tag_label" value="{{ old('tag_label', $serviceGroup->tag_label) }}" placeholder="e.g. Premium" class="form-input">
            </div>

            <div>
              <label class="form-label font-semibold">Description</label>
              <textarea name="description" rows="3" class="form-input resize-none">{{ old('description', $serviceGroup->description) }}</textarea>
            </div>

            <div>
              <label class="form-label font-semibold">Sort Order</label>
              <input type="number" name="sort_order" value="{{ old('sort_order', $serviceGroup->sort_order) }}" min="0" class="form-input">
            </div>
          </div>

          {{-- Right --}}
          <div class="space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Visibility
            </h3>

            <div>
              <label class="form-label font-semibold">Group Image</label>
              <div class="flex gap-4 items-start">
                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1">
                  <div class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
                  </div>
                  <p class="text-sm font-medium text-gray-600">Change image</p>
                  <input type="file" name="image" class="hidden" onchange="previewImg(this)" accept="image/*">
                </label>
                <div class="w-40 h-40 relative group overflow-hidden rounded-[2rem]">
                  <img id="img-preview"
                    src="{{ $serviceGroup->image ? asset('storage/' . $serviceGroup->image) : 'https://via.placeholder.com/160x160?text=No+Image' }}"
                    class="w-full h-full object-cover border border-gray-100" alt="">
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="text-[10px] text-white font-bold uppercase tracking-wider">Current</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
              <div>
                <p class="text-sm font-semibold text-gray-900">Active Status</p>
                <p class="text-xs text-gray-400 mt-0.5">Visible on booking platform</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="status" {{ $serviceGroup->status === 'Active' ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between px-10 py-6 bg-[#F9F9F9]/50">
        <form method="POST" action="{{ route('service-groups.destroy', $serviceGroup->id) }}" onsubmit="return confirm('Delete this group?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger text-sm px-6">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Group
          </button>
        </form>
        <div class="flex gap-3">
          <a href="{{ route('service-groups.index') }}" class="btn btn-secondary px-8">Cancel</a>
          <button type="submit" class="btn btn-primary lg:px-12 shadow-lg shadow-black/10">
            <i data-lucide="save" class="w-4 h-4"></i> Save Updates
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    function previewImg(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('img-preview').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endpush
