@extends('layouts.app')
@php $pageTitle = 'Create Package'; @endphp

@section('content')
<div class="flex flex-col gap-6">

  <!-- Page Header -->
  <div class="flex items-center gap-4">
    <a href="{{ route('packages.index') }}"
      class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
      <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
    </a>
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Create Package</h2>
      <p class="text-sm text-gray-400 mt-0.5">Bundle multiple services into one package</p>
    </div>
  </div>

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('packages.store') }}" enctype="multipart/form-data" id="packageForm">
    @csrf

    <!-- ━━━ SECTION 1 · PACKAGE DETAILS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Package Details</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left: Name + Category -->
          <div class="space-y-5">
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Package Name <span class="text-red-400">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Bridal Glow Package" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white transition-all" required>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Category <span class="text-red-400">*</span></label>
              <select name="category" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer transition-all" required>
                <option value="">Select category</option>
                @foreach(['Bridal', 'Hair', 'Makeup', 'Nails', 'Skincare', 'Wellness'] as $cat)
                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- Right: Image Upload -->
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Package Image <span class="text-red-400">*</span></label>
            <div id="dropZone1" class="drop-zone relative flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                 onclick="document.getElementById('packageImageInput').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-black', 'bg-white')"
                 ondragleave="this.classList.remove('border-black', 'bg-white')"
                 ondrop="handleImageDrop(event, 'packageImageInput')">
              <div id="uploadPlaceholder1" class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mb-3 group-hover:bg-gray-200 transition-colors">
                  <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">Click to upload or drag &amp; drop</p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 2MB</p>
              </div>
              <input type="file" name="package_image" id="packageImageInput" accept="image/*" class="hidden" onchange="previewImg(this, 'packageImgPreview', 'uploadPlaceholder1', 'dropZone1')">
            </div>
            <div class="relative mt-3 hidden" id="packageImgPreviewWrap">
              <img id="packageImgPreview" class="w-full h-44 object-cover rounded-2xl border border-gray-100 shadow-sm" src="" alt="">
              <button type="button" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('packageImageInput','packageImgPreview','packageImgPreviewWrap','uploadPlaceholder1','dropZone1')">
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ━━━ SECTION 2 · INCLUDED SERVICES ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Included Services</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-5">

        <!-- Service Search + Add -->
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Select Service</label>
          <div class="flex items-center gap-3">
            <div class="relative flex-1">
              <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
              <input type="text" id="serviceSearchInput" placeholder="Search services…" autocomplete="off"
                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white transition-all" 
                onfocus="showServiceDropdown()" oninput="filterServiceDropdown()">
              <!-- Dropdown -->
              <div id="serviceDropdown" class="hidden absolute top-full left-0 right-0 z-40 max-height-[240px] overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg mt-1">
                @foreach($services as $svc)
                <div class="svc-dropdown-item px-4 py-3 cursor-pointer flex items-center justify-between hover:bg-gray-50 transition-colors"
                     data-id="{{ $svc->id }}"
                     data-name="{{ $svc->name }}"
                     data-duration="{{ $svc->duration }}"
                     data-price="{{ $svc->price }}"
                     onclick="selectService(this)">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ $svc->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $svc->duration }} min</p>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">₹{{ number_format($svc->price) }}</span>
                </div>
                @endforeach
                <div id="noResultsItem" class="hidden px-4 py-3 text-sm text-gray-400 text-center">No services found</div>
              </div>
            </div>
            <button type="button" id="addServiceBtn" onclick="addSelectedService()" disabled
              class="px-5 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap">
              <i data-lucide="plus" class="w-4 h-4"></i> Add Service
            </button>
          </div>
        </div>

        <!-- Selected Services List -->
        <div>
          <div id="selectedServicesHeader" class="hidden flex items-center gap-3 mb-3 px-1">
            <span class="flex-1 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Service</span>
            <span class="w-24 text-[10px] font-semibold text-gray-400 uppercase tracking-widest text-center">Duration</span>
            <span class="w-28 text-[10px] font-semibold text-gray-400 uppercase tracking-widest text-right">Price</span>
            <span class="w-10"></span>
          </div>
          <div id="selectedServicesList" class="space-y-2">
            <!-- Selected services will appear here -->
          </div>
          <!-- Empty state -->
          <div id="noServicesState" class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
              <i data-lucide="package" class="w-6 h-6 text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-500">No services added yet</p>
            <p class="text-xs text-gray-400 mt-1">Search and add services to build your package</p>
          </div>
        </div>

        <!-- Hidden inputs -->
        <div id="hiddenServiceInputs"></div>
      </div>
    </div>

    <!-- ━━━ SECTION 3 · PACKAGE PRICING ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">3</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Package Pricing</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Total Original Price</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">₹</span>
              <input type="text" id="totalOriginalPrice" value="0" class="w-full px-8 py-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-500 cursor-not-allowed text-sm font-medium" readonly>
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Sum of all included services</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Total Duration</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">
                <i data-lucide="clock" class="w-4 h-4"></i>
              </span>
              <input type="text" id="totalDurationDisplay" value="0 min" class="w-full px-10 py-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-500 cursor-not-allowed text-sm font-medium" readonly>
              <input type="hidden" name="duration" id="totalDurationHidden" value="0">
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Combined duration of all services</p>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Package Price <span class="text-red-400">*</span></label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-semibold">₹</span>
            <input type="number" name="package_price" id="packagePriceInput" value="" placeholder="0.00" min="0" step="0.01"
              class="w-full px-8 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-lg font-bold text-gray-900 transition-all" 
              oninput="updateSavings()" required>
          </div>
          <p class="text-[11px] text-gray-400 mt-1.5 ml-1">The final price customers will pay</p>
        </div>

        <!-- Savings display -->
        <div id="savingsDisplay" class="hidden">
          <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <i data-lucide="trending-down" class="w-5 h-5 text-emerald-600"></i>
              </div>
              <div>
                <p class="text-sm font-semibold text-emerald-800">Customer Savings</p>
                <p class="text-xs text-emerald-600">Discount applied to package</p>
              </div>
            </div>
            <div class="text-right">
              <p id="savingsAmount" class="text-lg font-bold text-emerald-700">₹0</p>
              <p id="savingsPercent" class="text-xs font-semibold text-emerald-600">0% off</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ━━━ SECTION 4 · PACKAGE DESCRIPTION ━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">4</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Package Description</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-5">
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Description Title</label>
          <input type="text" name="desc_title" placeholder="e.g. Complete Bridal Glow Experience" 
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white transition-all">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Description Content</label>
          <div class="border border-gray-200 rounded-2xl overflow-hidden focus-within:border-black/50 transition-all">
            <div class="flex items-center gap-1 px-3 py-2 bg-gray-50/80 border-b border-gray-200">
              <button type="button" onclick="execCmd('bold', 'descEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bold"><b>B</b></button>
              <button type="button" onclick="execCmd('italic', 'descEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Italic"><i>I</i></button>
              <button type="button" onclick="execCmd('underline', 'descEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Underline"><u>U</u></button>
              <div class="w-px h-5 bg-gray-200 mx-1"></div>
              <button type="button" onclick="execCmd('insertUnorderedList', 'descEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bullet List"><i data-lucide="list" class="w-4 h-4"></i></button>
            </div>
            <div id="descEditor" contenteditable="true" class="px-5 py-4 min-h-[160px] text-sm text-gray-800 outline-none" data-placeholder="Describe this package and what's included…"></div>
            <input type="hidden" name="desc_content" id="descContentHidden">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Description Image <span class="text-gray-400 font-normal normal-case">(Optional)</span></label>
          <div id="dropZoneDesc" class="drop-zone flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
               onclick="document.getElementById('descImageInput').click()"
               ondragover="event.preventDefault(); this.classList.add('border-black', 'bg-white')"
               ondragleave="this.classList.remove('border-black', 'bg-white')"
               ondrop="handleImageDrop(event, 'descImageInput')">
            <div id="uploadPlaceholderDesc" class="flex flex-col items-center">
              <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors">
                <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
              </div>
              <p class="text-sm text-gray-500">Upload description image</p>
            </div>
            <input type="file" name="desc_image" id="descImageInput" accept="image/*" class="hidden" onchange="previewImg(this, 'descImgPreview', 'uploadPlaceholderDesc', 'dropZoneDesc')">
          </div>
          <div class="relative mt-3 hidden" id="descImgPreviewWrap">
            <img id="descImgPreview" class="w-full h-40 object-cover rounded-2xl border border-gray-100 shadow-sm" src="" alt="">
            <button type="button" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('descImageInput','descImgPreview','descImgPreviewWrap','uploadPlaceholderDesc','dropZoneDesc')">
              <i data-lucide="x" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ━━━ SECTION 5 · AFTERCARE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">5</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Aftercare Instructions</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-5">
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Aftercare Content</label>
          <div class="border border-gray-200 rounded-2xl overflow-hidden focus-within:border-black/50 transition-all">
            <div class="flex items-center gap-1 px-3 py-2 bg-gray-50/80 border-b border-gray-200">
              <button type="button" onclick="execCmd('bold', 'aftercareEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bold"><b>B</b></button>
              <button type="button" onclick="execCmd('italic', 'aftercareEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Italic"><i>I</i></button>
              <button type="button" onclick="execCmd('insertUnorderedList', 'aftercareEditor')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="List"><i data-lucide="list" class="w-4 h-4"></i></button>
            </div>
            <div id="aftercareEditor" contenteditable="true" class="px-5 py-4 min-h-[120px] text-sm text-gray-800 outline-none" data-placeholder="Post-service care instructions…"></div>
            <input type="hidden" name="aftercare_content" id="aftercareContentHidden">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Aftercare Image <span class="text-gray-400 font-normal normal-case">(Optional)</span></label>
          <div id="dropZoneAfter" class="drop-zone flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
               onclick="document.getElementById('afterImageInput').click()"
               ondragover="event.preventDefault(); this.classList.add('border-black', 'bg-white')"
               ondragleave="this.classList.remove('border-black', 'bg-white')"
               ondrop="handleImageDrop(event, 'afterImageInput')">
            <div id="uploadPlaceholderAfter" class="flex flex-col items-center">
              <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors">
                <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
              </div>
              <p class="text-sm text-gray-500">Upload image</p>
            </div>
            <input type="file" name="aftercare_image" id="afterImageInput" accept="image/*" class="hidden" onchange="previewImg(this, 'afterImgPreview', 'uploadPlaceholderAfter', 'dropZoneAfter')">
          </div>
          <div class="relative mt-3 hidden" id="afterImgPreviewWrap">
            <img id="afterImgPreview" class="w-full h-36 object-cover rounded-2xl border border-gray-100 shadow-sm" src="" alt="">
            <button type="button" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('afterImageInput','afterImgPreview','afterImgPreviewWrap','uploadPlaceholderAfter','dropZoneAfter')">
              <i data-lucide="x" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- STICKY ACTION BAR -->
    <div class="sticky bottom-8 z-30 bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-xl px-8 py-4 flex items-center justify-end gap-3 mt-8">
      <a href="{{ route('packages.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
      <button type="submit" name="form_action" value="draft" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-2">
        <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
      </button>
      <button type="submit" name="form_action" value="publish" 
        class="px-8 py-2.5 rounded-xl bg-black text-white text-sm font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/10 flex items-center gap-2"
        onclick="prepareForm()">
        <i data-lucide="globe" class="w-4 h-4"></i> Publish Package
      </button>
    </div>

  </form>
</div>
@endsection

@push('styles')
<style>
  .section-card { transition: all 0.25s ease; }
  .section-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,0.04); }
  [contenteditable]:empty:before { content: attr(data-placeholder); color: #9ca3af; font-style: italic; }
</style>
@endpush

@push('scripts')
<script>
  // --- Service Selection ---
  let selectedServices = [];

  function showServiceDropdown() {
    document.getElementById('serviceDropdown').classList.remove('hidden');
    filterServiceDropdown();
  }

  function filterServiceDropdown() {
    const q = document.getElementById('serviceSearchInput').value.toLowerCase();
    const items = document.querySelectorAll('.svc-dropdown-item');
    let visible = 0;
    items.forEach(item => {
      const name = item.dataset.name.toLowerCase();
      const id = parseInt(item.dataset.id);
      const isSelected = selectedServices.some(s => s.id === id);
      if (name.includes(q) && !isSelected) {
        item.classList.remove('hidden');
        visible++;
      } else {
        item.classList.add('hidden');
      }
    });
    document.getElementById('noResultsItem').classList.toggle('hidden', visible > 0);
  }

  let pendingService = null;
  function selectService(el) {
    pendingService = {
      id: parseInt(el.dataset.id),
      name: el.dataset.name,
      duration: parseInt(el.dataset.duration),
      price: parseInt(el.dataset.price)
    };
    document.getElementById('serviceSearchInput').value = el.dataset.name;
    document.getElementById('serviceDropdown').classList.add('hidden');
    document.getElementById('addServiceBtn').disabled = false;
  }

  function addSelectedService() {
    if (!pendingService) return;
    selectedServices.push({...pendingService});
    pendingService = null;
    document.getElementById('serviceSearchInput').value = '';
    document.getElementById('addServiceBtn').disabled = true;
    renderSelectedServices();
    recalcTotals();
  }

  function removeSelectedService(id) {
    selectedServices = selectedServices.filter(s => s.id !== id);
    renderSelectedServices();
    recalcTotals();
  }

  function renderSelectedServices() {
    const list = document.getElementById('selectedServicesList');
    const header = document.getElementById('selectedServicesHeader');
    const empty = document.getElementById('noServicesState');
    const hidden = document.getElementById('hiddenServiceInputs');

    list.innerHTML = '';
    hidden.innerHTML = '';

    if (selectedServices.length === 0) {
      header.classList.add('hidden');
      empty.classList.remove('hidden');
      return;
    }

    header.classList.remove('hidden');
    empty.classList.add('hidden');

    selectedServices.forEach((svc, idx) => {
      hidden.innerHTML += `<input type="hidden" name="service_ids[]" value="${svc.id}">`;
      const row = document.createElement('div');
      row.className = 'flex items-center gap-3 px-4 py-3.5 border border-gray-100 rounded-2xl bg-white hover:border-gray-200 transition-all';
      row.innerHTML = `
        <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-400">${idx+1}</div>
        <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">${svc.name}</p></div>
        <div class="w-24 text-center"><span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-50 px-2.5 py-1 rounded-full">${svc.duration} min</span></div>
        <div class="w-28 text-right"><span class="text-sm font-bold text-gray-700">₹${svc.price.toLocaleString('en-IN')}</span></div>
        <button type="button" onclick="removeSelectedService(${svc.id})" class="w-8 h-8 rounded-xl border border-gray-100 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center flex-shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        </button>
      `;
      list.appendChild(row);
    });
  }

  function recalcTotals() {
    const totalP = selectedServices.reduce((sum, s) => sum + s.price, 0);
    const totalD = selectedServices.reduce((sum, s) => sum + s.duration, 0);

    document.getElementById('totalOriginalPrice').value = totalP.toLocaleString('en-IN');
    const hrs = Math.floor(totalD / 60), mins = totalD % 60;
    document.getElementById('totalDurationDisplay').value = hrs > 0 ? `${hrs}h ${mins}m` : `${totalD} min`;
    document.getElementById('totalDurationHidden').value = totalD;

    updateSavings();
  }

  function updateSavings() {
    const original = selectedServices.reduce((sum, s) => sum + s.price, 0);
    const pkgPrice = parseFloat(document.getElementById('packagePriceInput').value) || 0;
    const savingsEl = document.getElementById('savingsDisplay');

    if (pkgPrice > 0 && original > 0 && pkgPrice < original) {
      const saved = original - pkgPrice;
      const pct = Math.round((saved / original) * 100);
      document.getElementById('savingsAmount').textContent = `₹${saved.toLocaleString('en-IN')}`;
      document.getElementById('savingsPercent').textContent = `${pct}% off`;
      savingsEl.classList.remove('hidden');
    } else {
      savingsEl.classList.add('hidden');
    }
  }

  // --- Image Helpers ---
  function previewImg(input, previewId, placeholderId, dropZoneId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById(previewId).src = e.target.result;
      document.getElementById(previewId + 'Wrap').classList.remove('hidden');
      document.getElementById(placeholderId).classList.add('hidden');
      document.getElementById(dropZoneId).classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function removeImage(inputId, previewId, wrapId, placeholderId, dropZoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(wrapId).classList.add('hidden');
    document.getElementById(placeholderId).classList.remove('hidden');
    document.getElementById(dropZoneId).classList.remove('hidden');
  }

  function handleImageDrop(e, inputId) {
    e.preventDefault();
    const input = document.getElementById(inputId);
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      input.dispatchEvent(new Event('change'));
    }
  }

  // --- Utils ---
  function execCmd(cmd, editorId) {
    document.getElementById(editorId).focus();
    document.execCommand(cmd, false, null);
  }

  function prepareForm() {
    document.getElementById('descContentHidden').value = document.getElementById('descEditor').innerHTML;
    document.getElementById('aftercareContentHidden').value = document.getElementById('aftercareEditor').innerHTML;
  }

  // Close dropdown on click outside
  document.addEventListener('click', e => {
    if (!e.target.closest('#serviceDropdown') && e.target.id !== 'serviceSearchInput') {
      document.getElementById('serviceDropdown').classList.add('hidden');
    }
  });

  lucide.createIcons();
</script>
@endpush
