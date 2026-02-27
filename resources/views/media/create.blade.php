@extends('layouts.app')
@php $pageTitle = 'Upload Media'; @endphp

@section('content')
<div class="flex flex-col gap-6">

  <!-- Page Header -->
  <div class="flex items-center gap-4">
    <a href="{{ route('media.index') }}"
      class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
      <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
    </a>
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Upload Media</h2>
      <p class="text-sm text-gray-400 mt-0.5">Add a new image or video to the media library</p>
    </div>
  </div>

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" id="mediaForm">
    @csrf
    <div class="flex flex-col gap-6">

      <!-- CARD 1 · MEDIA DETAILS -->
      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
        <div class="px-8 pt-7 pb-2">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1</div>
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Media Details</h3>
            <div class="flex-1 h-px bg-gray-100 ml-2"></div>
          </div>
        </div>
        <div class="px-8 pb-8">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <!-- Media Type -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Media Type <span class="text-red-400">*</span></label>
              <select name="media_type" id="media-type" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer transition-all">
                <option value="">Select type...</option>
                <option value="banner" {{ old('media_type', request('type')) === 'banner' ? 'selected' : '' }}>Image (Banner)</option>
                <option value="video" {{ old('media_type', request('type')) === 'video' ? 'selected' : '' }}>Video</option>
              </select>
            </div>

            <!-- Title -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Title <span class="text-red-400">*</span></label>
              <input type="text" name="title" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white transition-all" 
                placeholder="e.g. Hero Slide 1, Promo Video"
                value="{{ old('title') }}">
            </div>

            <!-- Linked Section -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Linked Section <span class="text-red-400">*</span></label>
              <select name="linked_section" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer transition-all">
                <option value="">Select section...</option>
                @foreach(['Top Banner','Mid Section','Side Gallery','Bottom Footer','Hero Slider','Services'] as $s)
                <option value="{{ $s }}" {{ (old('linked_section', request('section')) === $s) ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>

            <!-- Target Page -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Target Page <span class="text-red-400">*</span></label>
              <select name="target_page" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer transition-all">
                <option value="">Select page...</option>
                @foreach(['Home','Services','Packages','About Us','Contact','Professionals','Offers'] as $pg)
                <option value="{{ $pg }}" {{ (old('target_page', request('page')) === $pg) ? 'selected' : '' }}>{{ $pg }}</option>
                @endforeach
              </select>
            </div>

            <!-- Display Order -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Display Order</label>
              <input type="number" name="order" min="0"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white transition-all" 
                placeholder="e.g. 1"
                value="{{ old('order', '1') }}">
            </div>

            <!-- Status -->
            <div class="flex items-start pt-1">
              <div class="w-full py-3 px-4 bg-gray-50 rounded-xl flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">Active</p>
                  <p class="text-xs text-gray-400">Visible to customers</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" name="status" class="sr-only peer" {{ old('status', true) ? 'checked' : '' }}>
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                </label>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- CARD 2 · FILE UPLOAD -->
      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
        <div class="px-8 pt-7 pb-2">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Upload File</h3>
            <div class="flex-1 h-px bg-gray-100 ml-2"></div>
          </div>
        </div>
        <div class="px-8 pb-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <!-- Main Upload Zone -->
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Media File <span class="text-red-400">*</span></label>

              <!-- Type hint: image -->
              <div id="hint-image" class="{{ request('type') === 'banner' ? '' : 'hidden' }} mb-3 flex items-center gap-2 px-4 py-2.5 bg-blue-50 rounded-xl">
                <i data-lucide="image" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                <p class="text-xs text-blue-700 font-medium">Upload high-quality landscape images (1920×1080) for best results.</p>
              </div>
              <!-- Type hint: video -->
              <div id="hint-video" class="{{ request('type') === 'video' ? '' : 'hidden' }} mb-3 flex items-center gap-2 px-4 py-2.5 bg-violet-50 rounded-xl">
                <i data-lucide="video" class="w-4 h-4 text-violet-500 flex-shrink-0"></i>
                <p class="text-xs text-violet-700 font-medium">Portrait/Reel videos (9:16) work best for mobile. MP4 recommended.</p>
              </div>

              <div id="mainDropZone"
                class="relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2x cursor-pointer bg-gray-50/50 min-h-[200px] transition-all hover:bg-gray-100/50 hover:border-gray-300"
                onclick="document.getElementById('file-input').click()"
                ondragover="event.preventDefault(); this.classList.add('border-black', 'bg-white')"
                ondragleave="this.classList.remove('border-black', 'bg-white')"
                ondrop="handleMainDrop(event)">
                <div id="main-placeholder" class="flex flex-col items-center gap-3 py-8 px-4 text-center">
                  <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                    <i data-lucide="upload-cloud" class="w-7 h-7 text-gray-400"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">Click to upload or drag &amp; drop</p>
                    <p id="upload-sub-text" class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, MP4, WebM — Max 50MB</p>
                  </div>
                </div>
                <input type="file" id="file-input" name="media_file" class="hidden"
                  accept="{{ request('type') === 'banner' ? 'image/*' : (request('type') === 'video' ? 'video/*' : 'image/*,video/*') }}"
                  onchange="previewUpload(this)">
              </div>

              <!-- Image preview -->
              <div class="relative mt-3 hidden" id="imgPreviewWrap">
                <img id="preview-img" class="w-full max-h-52 object-cover rounded-2xl border border-gray-100" src="" alt="">
                <button type="button" onclick="removeMainFile()"
                  class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm">
                  <i data-lucide="x" class="w-4 h-4"></i>
                </button>
              </div>
            </div>

            <!-- Supported formats + Thumbnail -->
            <div class="space-y-5">

              <!-- Supported formats card -->
              <div class="bg-gray-50 rounded-2xl p-5 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Supported Formats</p>
                <div class="grid grid-cols-2 gap-2.5 text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-blue-400 flex-shrink-0"></i>
                    <span>JPG, PNG, GIF, WebP</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <i data-lucide="video" class="w-4 h-4 text-violet-400 flex-shrink-0"></i>
                    <span>MP4, WebM, MOV</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <i data-lucide="hard-drive" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                    <span>Max 50MB</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <i data-lucide="maximize" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                    <span>1920×1080 recommended</span>
                  </div>
                </div>
              </div>

              <!-- Thumbnail upload (video only) -->
              <div id="thumbnail-wrap" class="{{ request('type') === 'video' ? '' : 'hidden' }}">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Video Thumbnail <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <div id="thumbDropZone"
                  class="relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer bg-gray-50/50 min-h-[140px] transition-all hover:bg-gray-100/50 hover:border-gray-300"
                  onclick="document.getElementById('thumb-input').click()"
                  ondragover="event.preventDefault(); this.classList.add('border-black', 'bg-white')"
                  ondragleave="this.classList.remove('border-black', 'bg-white')"
                  ondrop="handleThumbDrop(event)">
                  <div id="thumb-placeholder" class="flex flex-col items-center gap-2 py-5 px-4 text-center">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                      <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Upload thumbnail image</p>
                    <p class="text-xs text-gray-400">JPG, PNG — Max 5MB</p>
                  </div>
                  <input type="file" id="thumb-input" name="thumbnail" class="hidden" accept="image/*" onchange="previewThumb(this)">
                </div>
                <div class="relative mt-2 hidden" id="thumbPreviewWrap">
                  <img id="thumb-img" class="w-full h-32 object-cover rounded-2xl border border-gray-100" src="" alt="">
                  <button type="button" onclick="removeThumb()"
                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- STICKY ACTION BAR -->
      <div class="sticky bottom-8 z-30 bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-xl px-8 py-4 flex items-center justify-end gap-3">
        <a href="{{ route('media.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
        <button type="submit" class="px-8 py-2.5 rounded-xl bg-black text-white text-sm font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/10 flex items-center gap-2">
          <i data-lucide="upload-cloud" class="w-4 h-4 text-white"></i> Upload Media
        </button>
      </div>

    </div>
  </form>

</div>
@endsection

@push('styles')
<style>
  .section-card { transition: all 0.25s ease; }
  .section-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,0.04); }
</style>
@endpush

@push('scripts')
<script>
  // Toggle thumbnail & hints when type changes
  document.getElementById('media-type').addEventListener('change', function() {
    const isVideo = this.value === 'video';
    const isImage = this.value === 'banner';

    document.getElementById('thumbnail-wrap').classList.toggle('hidden', !isVideo);
    document.getElementById('hint-video').classList.toggle('hidden', !isVideo);
    document.getElementById('hint-image').classList.toggle('hidden', !isImage);

    const input = document.getElementById('file-input');
    const subText = document.getElementById('upload-sub-text');
    if (isVideo) {
      input.accept = 'video/*';
      subText.textContent = 'MP4, WebM, MOV — Max 50MB';
    } else if (isImage) {
      input.accept = 'image/*';
      subText.textContent = 'JPG, PNG, GIF, WebP — Max 20MB';
    } else {
      input.accept = 'image/*,video/*';
      subText.textContent = 'JPG, PNG, GIF, MP4, WebM — Max 50MB';
    }
  });

  // Main file preview
  function previewUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('imgPreviewWrap').classList.remove('hidden');
        document.getElementById('main-placeholder').classList.add('hidden');
        document.getElementById('mainDropZone').classList.add('border-emerald-500', 'bg-emerald-50/30');
      };
      reader.readAsDataURL(file);
    } else {
      // Video — show filename
      document.getElementById('main-placeholder').innerHTML = `
        <div class="flex flex-col items-center gap-3 py-8 px-4 text-center">
          <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center">
            <i data-lucide="video" class="w-7 h-7 text-violet-500"></i>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700">${file.name}</p>
            <p class="text-xs text-gray-400 mt-1">${(file.size / (1024*1024)).toFixed(1)} MB</p>
          </div>
        </div>`;
      document.getElementById('mainDropZone').classList.add('border-emerald-500', 'bg-emerald-50/30');
      lucide.createIcons();
    }
  }

  function removeMainFile() {
    document.getElementById('file-input').value = '';
    document.getElementById('imgPreviewWrap').classList.add('hidden');
    const placeholder = document.getElementById('main-placeholder');
    placeholder.classList.remove('hidden');
    placeholder.innerHTML = `
      <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
        <i data-lucide="upload-cloud" class="w-7 h-7 text-gray-400"></i>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-700">Click to upload or drag &amp; drop</p>
        <p id="upload-sub-text" class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, MP4, WebM — Max 50MB</p>
      </div>
    `;
    document.getElementById('mainDropZone').classList.remove('border-emerald-500', 'bg-emerald-50/30');
    document.getElementById('preview-img').src = '';
    lucide.createIcons();
  }

  function handleMainDrop(e) {
    e.preventDefault();
    document.getElementById('mainDropZone').classList.remove('border-black', 'bg-white');
    const input = document.getElementById('file-input');
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      previewUpload(input);
    }
  }

  // Thumbnail preview
  function previewThumb(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('thumb-img').src = e.target.result;
      document.getElementById('thumbPreviewWrap').classList.remove('hidden');
      document.getElementById('thumb-placeholder').classList.add('hidden');
      document.getElementById('thumbDropZone').classList.add('border-emerald-500', 'bg-emerald-50/30');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function removeThumb() {
    document.getElementById('thumb-input').value = '';
    document.getElementById('thumbPreviewWrap').classList.add('hidden');
    document.getElementById('thumb-placeholder').classList.remove('hidden');
    document.getElementById('thumbDropZone').classList.remove('border-emerald-500', 'bg-emerald-50/30');
    document.getElementById('thumb-img').src = '';
  }

  function handleThumbDrop(e) {
    e.preventDefault();
    document.getElementById('thumbDropZone').classList.remove('border-black', 'bg-white');
    const input = document.getElementById('thumb-input');
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      previewThumb(input);
    }
  }
</script>
@endpush
