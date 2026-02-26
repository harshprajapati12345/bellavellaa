@extends('layouts.app')
@php $pageTitle = 'Edit Category'; @endphp

@section('content')
    <div class="flex flex-col gap-6">
      <div class="flex items-center gap-4">
        <a href="{{ route('categories.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Category</h2>
          <p class="text-sm text-gray-400 mt-0.5">Editing: <span class="text-black font-medium">{{ $category->name }}</span></p>
        </div>
      </div>

      @if(session('success'))
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium shadow-sm"><i data-lucide="check-circle" class="w-5 h-5"></i> Category updated successfully!</div>
      @endif
      @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm"><ul class="space-y-1">@foreach($errors->all() as $e)<li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>@endforeach</ul></div>
      @endif

      <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data" class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
        @csrf @method('PUT')
        <div class="p-8 lg:p-10 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Basic Details
                    </h3>
                    <div>
                      <label class="form-label font-semibold">Category Name *</label>
                      <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-input" required>
                    </div>
                    <div>
                      <label class="form-label font-semibold">Description</label>
                      <textarea name="description" rows="6" class="form-input resize-none">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>

                <div class="space-y-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Controls
                    </h3>
                    <div>
                      <label class="form-label font-semibold">Icon / Image</label>
                      <div class="flex gap-5 items-start">
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2.2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1 pb-2">
                            <div class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600">Change image</p>
                            <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </label>
                        <div class="w-40 h-40 relative group overflow-hidden rounded-[2.2rem]">
                            <img id="img-preview" src="{{ $category->image ? asset('storage/'.$category->image) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=800&q=80' }}" class="w-full h-full object-cover border border-gray-100" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-default">
                                <span class="text-[10px] text-white font-bold uppercase tracking-wider">Current Image</span>
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="flex items-center justify-between p-7 bg-[#F9F9F9] rounded-[2rem] border border-gray-50">
                      <div>
                        <p class="text-sm font-bold text-gray-900">Active Visibility</p>
                        <p class="text-xs text-gray-400 mt-1">Make this visible on frontend</p>
                      </div>
                      <label class="toggle-switch"><input type="checkbox" name="status" {{ ($category->status ?? 'Active') === 'Active' ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-between px-10 py-6 bg-[#F9F9F9]/50">
          <form method="POST" action="{{ route('categories.destroy', $category->id) }}" class="inline" onsubmit="return confirm('Delete this category?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger text-sm px-6">
              <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Category
            </button>
          </form>
          <div class="flex gap-3">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary px-8">Cancel</a>
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
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('img-preview').src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endpush
