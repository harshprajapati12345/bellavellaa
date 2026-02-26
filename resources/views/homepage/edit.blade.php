@extends('layouts.app')
@php $pageTitle = 'Edit Section'; @endphp

@section('content')

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
    <a href="{{ route('homepage.index') }}" class="hover:text-gray-600 transition-colors">Home Page Manager</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-gray-900 font-medium">Edit — {{ $homepage->content['name'] ?? $homepage->title }}</span>
  </nav>

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm mb-6">
    <ul class="list-disc list-inside">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('homepage.update', $homepage->id) }}" method="POST" enctype="multipart/form-data" id="sectionForm" class="max-w-4xl mx-auto">
    @csrf
    @method('PUT')

    <!-- Card 1: Basic Info -->
    <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 sm:p-8 mb-6">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
          <i data-lucide="settings-2" class="w-4 h-4 text-gray-400"></i>
          Section Details
        </h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg text-xs font-medium text-gray-500">
          <i data-lucide="hash" class="w-3.5 h-3.5"></i>
          Section #{{ $homepage->id }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Section Name <span class="text-red-400">*</span></label>
          <input type="text" name="name" required value="{{ old('name', $homepage->content['name'] ?? $homepage->title) }}"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Section Key <span class="text-red-400">*</span></label>
          <input type="text" value="{{ $homepage->section }}" readonly
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Content Type <span class="text-red-400">*</span></label>
          <select required name="content_type" id="content-type"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
            @php $ctype = old('content_type', $homepage->content['content_type'] ?? 'static'); @endphp
            <option value="static" {{ $ctype === 'static' ? 'selected' : '' }}>Static Content</option>
            <option value="dynamic" {{ $ctype === 'dynamic' ? 'selected' : '' }}>Dynamic (Linked to Data)</option>
          </select>
        </div>
        <div id="dynamic-source-wrap" class="{{ $ctype !== 'dynamic' ? 'hidden' : '' }}">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Data Source</label>
          @php $dataSource = old('data_source', $homepage->content['data_source'] ?? ''); @endphp
          <select name="data_source" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
            <option value="">Select source...</option>
            <option {{ $dataSource === 'Services' ? 'selected' : '' }}>Services</option>
            <option {{ $dataSource === 'Packages' ? 'selected' : '' }}>Packages</option>
            <option {{ $dataSource === 'Reviews / Testimonials' ? 'selected' : '' }}>Reviews / Testimonials</option>
            <option {{ $dataSource === 'Professionals' ? 'selected' : '' }}>Professionals</option>
            <option {{ $dataSource === 'Gallery / Media' ? 'selected' : '' }}>Gallery / Media</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <div class="flex items-center gap-3 mt-1">
            <label class="toggle-switch">
              <input type="checkbox" name="status" {{ $homepage->status === 'Active' ? 'checked' : '' }}>
              <span class="toggle-slider"></span>
            </label>
            <span class="text-sm text-gray-500 status-label">{{ $homepage->status }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Content -->
    <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 sm:p-8 mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
        <i data-lucide="type" class="w-4 h-4 text-gray-400"></i>
        Section Content
      </h2>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Section Title</label>
        <input type="text" name="title" value="{{ old('title', $homepage->title) }}"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
      </div>
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $homepage->content['subtitle'] ?? '') }}"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
      </div>
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden">
          <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
            <button type="button" onclick="execCmd('bold')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="bold" class="w-4 h-4"></i></button>
            <button type="button" onclick="execCmd('italic')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="italic" class="w-4 h-4"></i></button>
            <button type="button" onclick="execCmd('underline')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="underline" class="w-4 h-4"></i></button>
            <div class="w-px h-5 bg-gray-200 mx-1"></div>
            <button type="button" onclick="execCmd('insertUnorderedList')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="list" class="w-4 h-4"></i></button>
            <button type="button" onclick="execCmd('createLink')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="link" class="w-4 h-4"></i></button>
          </div>
          <div id="descEditor" contenteditable="true" class="min-h-[160px] px-4 py-4 text-sm text-gray-700 focus:outline-none leading-relaxed">{!! old('description', $homepage->content['description'] ?? '') !!}</div>
          <input type="hidden" name="description" id="descHidden">
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Button Text <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
          <input type="text" name="btn_text" value="{{ old('btn_text', $homepage->content['btn_text'] ?? '') }}"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Button Link <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
          <input type="text" name="btn_link" value="{{ old('btn_link', $homepage->content['btn_link'] ?? '') }}"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-3">
      <a href="{{ route('homepage.index') }}" class="px-6 py-3 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-white transition-colors">Cancel</a>
      <button type="submit" onclick="syncDesc()" class="px-8 py-3 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
        Update Section
      </button>
    </div>
  </form>

  <form id="delete-form" action="{{ route('homepage.destroy', $homepage->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>

@endsection

@push('styles')
<style>
  .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .toggle-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #e5e7eb; border-radius: 999px; transition: 0.25s;
  }
  .toggle-slider:before {
    content: ''; position: absolute;
    width: 16px; height: 16px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: 0.25s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  }
  input:checked + .toggle-slider { background: #000; }
  input:checked + .toggle-slider:before { transform: translateX(16px); }
</style>
@endpush

@push('scripts')
<script>
  // Status toggle label sync
  document.querySelector('input[name="status"]').addEventListener('change', function() {
    document.querySelector('.status-label').textContent = this.checked ? 'Active' : 'Inactive';
  });

  document.getElementById('content-type').addEventListener('change', function() {
    document.getElementById('dynamic-source-wrap').classList.toggle('hidden', this.value !== 'dynamic');
  });

  function execCmd(cmd) {
    if (cmd === 'createLink') {
      const url = prompt('Enter URL:');
      if (url) document.execCommand('createLink', false, url);
    } else {
      document.execCommand(cmd, false, null);
    }
    document.getElementById('descEditor').focus();
  }

  function syncDesc() {
    document.getElementById('descHidden').value = document.getElementById('descEditor').innerHTML;
  }
</script>
@endpush
