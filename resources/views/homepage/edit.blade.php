@extends('layouts.app')
@php $pageTitle = 'Edit Section'; @endphp

@section('content')

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
    <a href="{{ route('homepage.index') }}" class="hover:text-gray-600 transition-colors">Home Page Manager</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-gray-900 font-medium">Edit — {{ $homepage->name ?? $homepage->title }}</span>
  </nav>

  @if(session('success'))
    <div
      class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium mb-6">
      <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm mb-6">
    <ul class="list-disc list-inside">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="flex flex-col gap-6">

    <form method="POST" action="{{ route('homepage.update', $homepage->id) }}" enctype="multipart/form-data" id="sectionForm">
      @csrf
      @method('PUT')
      <input type="hidden" name="content_type" value="{{ old('content_type', $homepage->content_type ?? 'static') }}">
      <input type="hidden" name="data_source" value="{{ old('data_source', $homepage->data_source ?? '') }}">
      
      <div class="flex flex-col gap-6">

        <!-- CARD 1: SECTION DETAILS -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card max-w-4xl mx-auto w-full">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1
                </div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Section Details</h3>
                <div class="flex-1 h-px bg-gray-100 ml-2"></div>
              </div>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg text-xs font-medium text-gray-500">
                <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                Section #{{ $homepage->id }}
              </span>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

              <!-- Section Type (Locked — cannot be changed after creation) -->
              <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Section Type</label>
                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl">
                  <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="lock" class="w-4 h-4 text-gray-500"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $homepage->name ?? $homepage->section }}</p>
                    <p class="text-xs font-mono text-gray-400">{{ $homepage->section }}</p>
                  </div>
                  <span class="ml-auto text-xs text-gray-400 bg-white border border-gray-200 px-2.5 py-1 rounded-lg">Locked</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1 italic">Section type cannot be changed after creation.</p>
              </div>

              <!-- Title -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Section Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" required
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                  placeholder="e.g. Welcome to Bellavella" value="{{ old('title', $homepage->title) }}">
              </div>

              <!-- Subtitle -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subtitle <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" name="subtitle"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                  placeholder="e.g. Premium Salon &amp; Beauty Experience" value="{{ old('subtitle', $homepage->subtitle ?? '') }}">
              </div>

              <!-- Media Type -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Media Type <span class="text-red-400">*</span></label>
                <select name="media_type" required
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white cursor-pointer">
                  <option value="">Select media type...</option>
                  <option value="banner" {{ old('media_type', $homepage->media_type ?? '') === 'banner' ? 'selected' : '' }}>Banner (Image)</option>
                  <option value="video" {{ old('media_type', $homepage->media_type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
                </select>
              </div>

              <!-- Status -->
              <div class="flex items-start pt-1">
                <div class="w-full py-3 px-4 bg-gray-50 rounded-xl flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Active Section</p>
                    <p class="text-xs text-gray-400">Show this section on the homepage</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" name="status" {{ $homepage->status === 'Active' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>

            </div>
          </div>

        </div>

        <!-- STICKY ACTION BAR -->
        <div
          class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2 max-w-4xl mx-auto w-full">
          <a href="{{ route('homepage.index') }}"
            class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-white transition-colors">Cancel</a>
          <button type="submit" name="form_action" value="publish"
            class="px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="globe" class="w-4 h-4"></i> Update Section
          </button>
        </div>

      </div>
    </form>
  </div>

  <form id="delete-form" action="{{ route('homepage.destroy', $homepage->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>

@endsection

@push('styles')
  <style>
    .section-card {
      transition: box-shadow 0.25s ease;
    }

    .section-card:hover {
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
    }

    .sticky-bar {
      position: sticky;
      bottom: 0;
      z-index: 30;
      backdrop-filter: blur(12px);
      background: rgba(255, 255, 255, 0.88);
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 38px;
      height: 22px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background: #e5e7eb;
      border-radius: 999px;
      transition: 0.25s;
    }

    .toggle-slider:before {
      content: '';
      position: absolute;
      width: 16px;
      height: 16px;
      left: 3px;
      bottom: 3px;
      background: white;
      border-radius: 50%;
      transition: 0.25s;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    input:checked+.toggle-slider {
      background: #000;
    }

    input:checked+.toggle-slider:before {
      transform: translateX(16px);
    }
  </style>
@endpush

@push('scripts')
  {{-- No client-side scripts needed for edit form --}}
@endpush
