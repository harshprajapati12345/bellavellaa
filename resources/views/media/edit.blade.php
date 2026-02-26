@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-6">
  @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <div class="flex items-center gap-4">
    <a href="{{ route('media.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
    <div><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Media</h2><p class="text-sm text-gray-400 mt-0.5">{{ $media->title }}</p></div>
  </div>
  <form method="POST" action="{{ route('media.update', $media->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6">
      <div class="px-8 pt-7 pb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-5">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Title</label><input type="text" name="title" value="{{ old('title', $media->title) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all" required></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label><select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all cursor-pointer"><option value="banner" {{ $media->type === 'banner' ? 'selected' : '' }}>Banner</option><option value="video" {{ $media->type === 'video' ? 'selected' : '' }}>Video</option></select></div>
          <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="status" {{ $media->status === 'Active' ? 'checked' : '' }} class="w-4 h-4 accent-black"><span class="text-sm text-gray-700">Active</span></label>
        </div>
        <div class="space-y-5">
          @if($media->file_url)<div class="aspect-video bg-gray-100 rounded-xl overflow-hidden"><img src="{{ $media->file_url }}" class="w-full h-full object-cover" alt=""></div>@endif
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Replace File</label><input type="file" name="file" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all"></div>
        </div>
      </div>
    </div>
    <div class="flex items-center justify-between">
      <div class="flex gap-3"><a href="{{ route('media.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-medium">Cancel</a><button type="submit" class="px-6 py-3 rounded-xl bg-black text-white font-medium hover:bg-gray-800 transition-all">Update</button></div>
    </div>
  </form>
  <div class="mt-4">
    <form action="{{ route('media.destroy', $media->id) }}" method="POST" id="delete-media-form" class="inline" onsubmit="return confirm('Really delete and remove this media permanently?')">
      @csrf 
      @method('DELETE')
      <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-all text-sm font-medium flex items-center gap-2">
        <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Media
      </button>
    </form>
  </div>
</div>
@endsection
