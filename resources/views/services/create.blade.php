@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <!-- Page Header -->
  <div class="flex items-center gap-4">
    <a href="{{ route('services.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
      <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
    </a>
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Service</h2>
      <p class="text-sm text-gray-400 mt-0.5">Create a new service offering</p>
    </div>
  </div>

  <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data" id="serviceForm">
    @csrf
  <div class="flex flex-col xl:flex-row gap-8 items-start">

    <!-- ━━━ FORM COLUMN ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="flex-1 min-w-0">

    <!-- ━━━ SECTION 1 · BASIC DETAILS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Basic Details</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left: Name + Category + Subcategory + Duration -->
          <div class="space-y-5">
            <div>
              <label class="form-label">Service Name <span class="text-red-400">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Bridal Makeup" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Main Category <span class="text-red-400">*</span></label>
              <select name="category_id" class="form-input cursor-pointer" onchange="pvUpdateCategory()">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label">Subcategory <span class="text-red-400">*</span></label>
              <select name="subcategory" class="form-input cursor-pointer">
                <option value="">Select Subcategory</option>
                @foreach($subcategories as $sub)
                <option value="{{ $sub }}" {{ old('subcategory') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label">Duration <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
              <select name="duration" class="form-input cursor-pointer">
                <option value="">Select Duration</option>
                @for($d = 5; $d <= 180; $d += 5)
                <option value="{{ $d }}" {{ old('duration') == $d ? 'selected' : '' }}>{{ $d }} min</option>
                @endfor
              </select>
            </div>
          </div>

          <!-- Right: Image Upload -->
          <div>
            <label class="form-label">Service Image <span class="text-red-400">*</span></label>
            <div id="dropZone1" class="drop-zone relative flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                 onclick="document.getElementById('serviceImageInput').click()"
                 ondragover="event.preventDefault(); this.classList.add('dragover')"
                 ondragleave="this.classList.remove('dragover')"
                 ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'serviceImageInput','serviceImgPreview')">
              <div id="uploadPlaceholder1" class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mb-3 group-hover:bg-gray-200 transition-colors">
                  <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">Click to upload or drag & drop</p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 2MB</p>
              </div>
              <input type="file" name="service_image" id="serviceImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'serviceImgPreview','uploadPlaceholder1','removeBtn1')">
            </div>
            <div class="relative mt-3 hidden" id="serviceImgPreviewWrap">
              <img id="serviceImgPreview" class="w-full h-48 object-cover rounded-2xl border border-gray-100" src="" alt="">
              <button type="button" id="removeBtn1" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('serviceImageInput','serviceImgPreview','serviceImgPreviewWrap','uploadPlaceholder1','dropZone1')">
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
          <label class="form-label">Description</label>
          <textarea name="description" rows="4" placeholder="Describe this service…" class="form-input resize-none">{{ old('description') }}</textarea>
        </div>
      </div>
    </div>

    <!-- ━━━ SECTION 2 · SERVICE PREVIEW ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Service Preview</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-6">
        <!-- Service Variations (Dynamic) -->
        <div>
          <label class="form-label">Service Types</label>
          <div id="serviceTypesContainer" class="space-y-4">
            <div class="service-type-row dyn-item rounded-2xl border border-gray-200 p-5">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                  <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider svc-label">Service 1</span>
                </div>
                <button type="button" onclick="removeServiceCard(this)" class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Service Name</label>
                  <input type="text" name="service_types[]" placeholder="e.g. Underarm Thread" class="form-input">
                </div>
                <div>
                  <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (₹) *</label>
                  <input type="number" name="service_prices[]" placeholder="0.00" min="0" step="0.01" class="form-input text-right svc-price">
                </div>
              </div>
              <div class="mb-4">
                <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Reviews</label>
                <input type="number" name="service_reviews[]" placeholder="e.g. 120" min="0" class="form-input">
              </div>
              <div class="inline-upload-wrap">
                <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Image</label>
                <label class="inline-upload-placeholder flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                  <p class="text-xs text-gray-400 mt-1.5">Upload image</p>
                  <input type="file" name="service_images[]" accept="image/*" class="hidden" onchange="previewInlineImg(this)">
                </label>
                <div class="relative mt-2">
                  <img class="inline-img-preview hidden w-full h-24 object-cover rounded-xl border border-gray-100" src="" alt="">
                  <button type="button" class="inline-remove-btn hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm" onclick="removeInlineImg(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <button type="button" onclick="addServiceType()" class="mt-4 flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors group">
            <div class="w-7 h-7 rounded-lg border border-dashed border-gray-300 flex items-center justify-center group-hover:border-black transition-colors">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            </div>
            Add Another Service
          </button>
        </div>
      </div>
    </div>

    <!-- ━━━ SECTION 3 · SERVICE DESCRIPTION ━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
      <div class="px-8 pt-7 pb-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">3</div>
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Service Description</h3>
          <div class="flex-1 h-px bg-gray-100 ml-2"></div>
        </div>
      </div>
      <div class="px-8 pb-8 space-y-5">
        <div>
          <label class="form-label">Description Title</label>
          <input type="text" name="desc_title" value="{{ old('desc_title') }}" placeholder="e.g. Premium Bridal Makeup Experience" class="form-input">
        </div>
        <div>
          <label class="form-label">Description Images <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
          <div id="descImagesContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <!-- slots added by JS -->
          </div>
          <button type="button" onclick="addDescImage()" class="mt-3 flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors group">
            <div class="w-7 h-7 rounded-lg border border-dashed border-gray-300 flex items-center justify-center group-hover:border-black transition-colors">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            </div>
            Add More Image
          </button>
        </div>
      </div>
    </div>

    <!-- ━━━ STICKY ACTION BAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2">
      <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" name="form_action" value="draft" class="btn btn-secondary">
        <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
      </button>
      <button type="submit" name="form_action" value="publish" class="btn btn-primary">
        <i data-lucide="globe" class="w-4 h-4"></i> Publish Service
      </button>
    </div>
    </div><!-- /form column -->

    <!-- ━━━ PHONE PREVIEW COLUMN ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <div class="hidden xl:block preview-sticky">
      <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest text-center">Live Preview</p>

      <div class="phone-shell">
        <div class="phone-notch"></div>
        <div class="phone-screen">
          <!-- Status bar -->
          <div class="phone-status">
            <span id="pv-time">10:00</span>
            <div class="flex items-center gap-1">
              <svg width="10" height="8" viewBox="0 0 10 8" fill="#111"><rect x="0" y="3" width="2" height="5" rx="1"/><rect x="3" y="2" width="2" height="6" rx="1"/><rect x="6" y="1" width="2" height="7" rx="1"/><rect x="9" y="0" width="1" height="8" rx="0.5"/></svg>
              <svg width="12" height="8" viewBox="0 0 12 8" fill="none" stroke="#111" stroke-width="1.2"><path d="M1 5.5a7.07 7.07 0 0 1 10 0"/><path d="M3.5 7.5a3.54 3.54 0 0 1 5 0"/><circle cx="6" cy="7.5" r="0.8" fill="#111" stroke="none"/></svg>
              <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><rect x="0.5" y="0.5" width="17" height="9" rx="2.5" stroke="#111"/><rect x="2" y="2" width="12" height="6" rx="1.5" fill="#111"/><path d="M18.5 3.5v3a1.5 1.5 0 0 0 0-3z" fill="#111"/></svg>
            </div>
          </div>

          <div class="phone-scroll">
            <!-- Hero image -->
            <div id="pv-hero-wrap">
              <div id="pv-hero-placeholder" class="preview-hero-placeholder">No Image</div>
              <img id="pv-hero" class="preview-hero hidden" src="" alt="">
            </div>

            <!-- Back button overlay -->
            <div style="display:flex;align-items:center;padding:10px 12px 4px;gap:6px;">
              <div style="width:22px;height:22px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
              </div>
              <span style="font-size:11px;font-weight:600;color:#111;" id="pv-navtitle">Service</span>
            </div>

            <!-- Service name & meta -->
            <div style="padding:4px 14px 10px;">
              <p id="pv-name" style="font-size:15px;font-weight:700;color:#111;line-height:1.3;margin-bottom:6px;">&mdash;</p>
              <div style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                <span id="pv-category" class="preview-badge" style="background:#fdf2f8;color:#be185d;">—</span>
                <span id="pv-subcategory" class="preview-badge" style="background:#f0fdf4;color:#15803d;display:none;"></span>
                <span id="pv-duration" class="preview-badge">— min</span>
              </div>
              <p id="pv-basic-desc" style="font-size:10px;color:#666;line-height:1.4;margin-top:8px;white-space:pre-wrap;"></p>
            </div>

            <div class="preview-divider"></div>

            <!-- Service types list -->
            <div style="padding:10px 14px;">
              <p style="font-size:9px;font-weight:700;color:#aaa;letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;">Services</p>
              <div id="pv-services" style="display:flex;flex-direction:column;gap:6px;"></div>
            </div>

            <div class="preview-divider"></div>

            <!-- Description -->
            <div style="padding:10px 14px;">
              <p id="pv-desc-title" style="font-size:12px;font-weight:700;color:#111;margin-bottom:8px;">About this service</p>
              <p id="pv-description" style="font-size:10px;color:#666;line-height:1.5;margin-top:4px;white-space:pre-wrap;"></p>
            </div>

            <!-- Description images -->
            <div id="pv-desc-imgs" style="display:flex;flex-direction:column;"></div>
            <div style="height:20px;"></div>

          </div><!-- /phone-scroll -->
        </div><!-- /phone-screen -->
      </div><!-- /phone-shell -->

      <p class="text-[10px] text-gray-300 text-center">Updates as you type</p>
    </div><!-- /preview column -->

  </div><!-- /flex row -->
  </form>

</div>
@endsection

@push('styles')
<style>
  .section-card { transition: box-shadow 0.25s ease; }
  .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
  .dyn-item { animation: slideUp 0.35s cubic-bezier(0.16,1,0.3,1); }
  @keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
  .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
  .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.85); }
  .form-input { width:100%; padding:0.75rem 1rem; border-radius:0.75rem; border:1px solid #e5e7eb; outline:none; transition:all 0.2s; background:white; font-size:0.875rem; }
  .form-input:focus { border-color:#000; }
  .form-label { display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.5rem; }
  .btn { padding:0.75rem 1.5rem; border-radius:0.75rem; font-weight:500; transition:all 0.2s; display:inline-flex; align-items:center; gap:0.5rem; font-size:0.875rem; }
  .btn-primary { background:#000; color:#fff; } .btn-primary:hover { background:#1f2937; }
  .btn-secondary { background:#fff; color:#374151; border:1px solid #e5e7eb; } .btn-secondary:hover { background:#f9fafb; }

  /* ── Phone mockup ─────────────────────────────────────────────────── */
  .phone-shell { width:270px; height:560px; border-radius:42px; background:#111; padding:14px 12px; box-shadow:0 0 0 2px #333, 0 24px 64px rgba(0,0,0,0.45), inset 0 0 0 2px #555; position:relative; flex-shrink:0; }
  .phone-notch { position:absolute; top:14px; left:50%; transform:translateX(-50%); width:80px; height:22px; background:#111; border-radius:0 0 14px 14px; z-index:10; }
  .phone-screen { width:100%; height:100%; border-radius:30px; background:#fff; overflow:hidden; display:flex; flex-direction:column; }
  .phone-scroll { flex:1; overflow-y:auto; scrollbar-width:none; scroll-behavior:smooth; }
  .phone-scroll::-webkit-scrollbar { display:none; }
  .phone-status { display:flex; align-items:center; justify-content:space-between; padding:10px 16px 4px; font-size:10px; font-weight:600; color:#111; flex-shrink:0; z-index:5; background:#fff; }
  .preview-hero { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; background:#f3f4f6; }
  .preview-hero-placeholder { width:100%; aspect-ratio:4/3; background:linear-gradient(135deg,#f8f8f8 0%,#ececec 100%); display:flex; align-items:center; justify-content:center; color:#ccc; font-size:11px; }
  .preview-desc-img { width:100%; display:block; object-fit:cover; }
  .preview-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:99px; font-size:9px; font-weight:600; letter-spacing:.03em; background:#f3f4f6; color:#555; }
  .preview-divider { height:1px; background:#f1f1f1; margin:0 14px; }
  .preview-sticky { position:sticky; top:2rem; display:flex; flex-direction:column; align-items:center; gap:16px; }
</style>
@endpush

@push('scripts')
<script>
  /* ═══════════════════════════════════════════════════════════════════════
     IMAGE UPLOAD HELPERS
     ═══════════════════════════════════════════════════════════════════════ */

  function previewImg(input, previewId, placeholderId, removeBtnId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById(previewId);
      img.src = e.target.result;
      const wrap = img.closest('[id$="PreviewWrap"]') || img.parentElement;
      wrap.classList.remove('hidden');
      document.getElementById(placeholderId).classList.add('hidden');
      // Update phone preview hero
      pvUpdateHero(e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }

  function removeImage(inputId, previewId, wrapId, placeholderId, dropZoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(wrapId).classList.add('hidden');
    document.getElementById(placeholderId).classList.remove('hidden');
    pvUpdateHero(null);
  }

  function handleDrop(e, inputId, previewId) {
    const dt = e.dataTransfer;
    if (dt.files && dt.files[0]) {
      const input = document.getElementById(inputId);
      input.files = dt.files;
      input.dispatchEvent(new Event('change'));
    }
  }

  function previewInlineImg(input) {
    const preview = input.closest('.inline-upload-wrap').querySelector('.inline-img-preview');
    const placeholder = input.closest('.inline-upload-wrap').querySelector('.inline-upload-placeholder');
    const removeBtn = input.closest('.inline-upload-wrap').querySelector('.inline-remove-btn');
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      placeholder.classList.add('hidden');
      if (removeBtn) removeBtn.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
    setTimeout(pvUpdateServices, 50);
  }

  function removeInlineImg(btn) {
    const wrap = btn.closest('.inline-upload-wrap');
    wrap.querySelector('input[type="file"]').value = '';
    wrap.querySelector('.inline-img-preview').classList.add('hidden');
    wrap.querySelector('.inline-img-preview').src = '';
    wrap.querySelector('.inline-upload-placeholder').classList.remove('hidden');
    btn.classList.add('hidden');
    setTimeout(pvUpdateServices, 50);
  }

  /* ═══════════════════════════════════════════════════════════════════════
     SERVICE VARIATIONS
     ═══════════════════════════════════════════════════════════════════════ */

  let svcCount = document.querySelectorAll('.service-type-row').length;

  function addServiceType() {
    svcCount++;
    const container = document.getElementById('serviceTypesContainer');
    const div = document.createElement('div');
    div.className = 'service-type-row dyn-item rounded-2xl border border-gray-200 p-5';
    div.innerHTML = `
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-gray-900"></div>
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider svc-label">Service ${svcCount}</span>
        </div>
        <button type="button" onclick="removeServiceCard(this)" class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Service Name</label>
          <input type="text" name="service_types[]" placeholder="e.g. Jawline Thread" class="form-input">
        </div>
        <div>
          <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (₹) *</label>
          <input type="number" name="service_prices[]" placeholder="0.00" min="0" step="0.01" class="form-input text-right svc-price">
        </div>
      </div>
      <div class="mb-4">
        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Reviews</label>
        <input type="number" name="service_reviews[]" placeholder="e.g. 120" min="0" class="form-input">
      </div>
      <div class="inline-upload-wrap">
        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Image</label>
        <label class="inline-upload-placeholder flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
          <p class="text-xs text-gray-400 mt-1.5">Upload image</p>
          <input type="file" name="service_images[]" accept="image/*" class="hidden" onchange="previewInlineImg(this)">
        </label>
        <div class="relative mt-2">
          <img class="inline-img-preview hidden w-full h-24 object-cover rounded-xl border border-gray-100" src="" alt="">
          <button type="button" class="inline-remove-btn hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm" onclick="removeInlineImg(this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
      </div>
    `;
    container.appendChild(div);
  }

  function removeServiceCard(btn) {
    const rows = document.querySelectorAll('#serviceTypesContainer .service-type-row');
    if (rows.length > 1) {
      btn.closest('.service-type-row').remove();
      renumberServices();
    }
  }

  function renumberServices() {
    document.querySelectorAll('#serviceTypesContainer .service-type-row').forEach((card, i) => {
      const label = card.querySelector('.svc-label');
      if (label) label.textContent = `Service ${i + 1}`;
    });
    svcCount = document.querySelectorAll('#serviceTypesContainer .service-type-row').length;
  }

  /* ═══════════════════════════════════════════════════════════════════════
     DESCRIPTION IMAGES (multi-slot grid)
     ═══════════════════════════════════════════════════════════════════════ */

  function buildDescSlot() {
    const slot = document.createElement('div');
    slot.className = 'desc-img-slot dyn-item relative group';
    slot.innerHTML = `
      <label class="desc-upload-label flex flex-col items-center justify-center w-full aspect-square border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        <p class="text-xs text-gray-400 mt-2">Upload image</p>
        <input type="file" name="desc_images[]" accept="image/*" class="hidden" onchange="previewDescImg(this)">
      </label>
      <img class="desc-preview hidden absolute inset-0 w-full h-full object-cover rounded-2xl border border-gray-100" src="" alt="">
      <button type="button" onclick="removeDescSlot(this)" class="desc-slot-remove hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm z-10">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    `;
    return slot;
  }

  function addDescImage() {
    document.getElementById('descImagesContainer').appendChild(buildDescSlot());
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
    updateDescRemoveButtons();
  }

  function previewDescImg(input) {
    const slot = input.closest('.desc-img-slot');
    const label = slot.querySelector('.desc-upload-label');
    const preview = slot.querySelector('.desc-preview');
    const removeBtn = slot.querySelector('.desc-slot-remove');
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      label.classList.add('hidden');
      removeBtn.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
    setTimeout(pvRebuildDescImgs, 50);
  }

  function removeDescSlot(btn) {
    const container = document.getElementById('descImagesContainer');
    const slots = container.querySelectorAll('.desc-img-slot');
    if (slots.length > 1) {
      btn.closest('.desc-img-slot').remove();
    } else {
      const slot = btn.closest('.desc-img-slot');
      slot.querySelector('input[type="file"]').value = '';
      slot.querySelector('.desc-preview').classList.add('hidden');
      slot.querySelector('.desc-preview').src = '';
      slot.querySelector('.desc-upload-label').classList.remove('hidden');
      btn.classList.add('hidden');
    }
    updateDescRemoveButtons();
    setTimeout(pvRebuildDescImgs, 50);
  }

  function updateDescRemoveButtons() {
    document.querySelectorAll('#descImagesContainer .desc-img-slot').forEach(slot => {
      const btn = slot.querySelector('.desc-slot-remove');
      const hasImage = !slot.querySelector('.desc-preview').classList.contains('hidden');
      if (hasImage) btn.classList.remove('hidden');
    });
  }

  // Init: add one empty slot on load
  addDescImage();

  /* ═══════════════════════════════════════════════════════════════════════
     LIVE PREVIEW ENGINE
     ═══════════════════════════════════════════════════════════════════════ */

  // ── Clock
  function pvClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const el = document.getElementById('pv-time');
    if (el) el.textContent = h + ':' + m;
  }
  pvClock(); setInterval(pvClock, 10000);

  // ── Name
  function pvUpdateName() {
    const v = document.querySelector('[name="name"]')?.value.trim();
    const el = document.getElementById('pv-name');
    if (el) el.textContent = v || '—';
    const nav = document.getElementById('pv-navtitle');
    if (nav) nav.textContent = v || 'Service';
  }

  // ── Category
  function pvUpdateCategory() {
    const sel = document.querySelector('[name="category_id"]');
    const v = sel?.options[sel.selectedIndex]?.text || '—';
    const el = document.getElementById('pv-category');
    if (el) el.textContent = v === 'Select Category' ? '—' : v;
  }

  // ── Subcategory
  function pvUpdateSubcategory() {
    const sel = document.querySelector('[name="subcategory"]');
    const v = sel?.options[sel.selectedIndex]?.value || '';
    const el = document.getElementById('pv-subcategory');
    if (!el) return;
    if (v) { el.textContent = v; el.style.display = 'inline-flex'; }
    else { el.style.display = 'none'; }
  }

  // ── Duration
  function pvUpdateDuration() {
    const sel = document.querySelector('[name="duration"]');
    const v = sel?.options[sel.selectedIndex]?.value || '';
    const el = document.getElementById('pv-duration');
    if (!el) return;
    if (v) { el.textContent = v + ' min'; el.style.display = ''; }
    else { el.style.display = 'none'; }
  }

  // ── Desc title
  function pvUpdateDescTitle() {
    const v = document.querySelector('[name="desc_title"]')?.value.trim();
    const el = document.getElementById('pv-desc-title');
    if (el) el.textContent = v || 'About this service';
  }

  // ── Description
  function pvUpdateDescription() {
    const v = document.querySelector('[name="description"]')?.value.trim();
    const el1 = document.getElementById('pv-description');
    const el2 = document.getElementById('pv-basic-desc');
    if (el1) el1.textContent = v || '';
    if (el2) el2.textContent = v || '';
  }

  // ── Service types — 2-col product cards
  function pvUpdateServices() {
    const container = document.getElementById('pv-services');
    if (!container) return;
    const rows = [...document.querySelectorAll('#serviceTypesContainer .service-type-row')];
    container.innerHTML = '';
    if (!rows.length) { container.innerHTML = '<span style="font-size:10px;color:#ccc;">No services added yet</span>'; return; }
    const grid = document.createElement('div');
    grid.style.cssText = 'display:grid;grid-template-columns:1fr 1fr;gap:8px;';
    rows.forEach(row => {
      const name = row.querySelector('[name="service_types[]"]')?.value.trim() || '—';
      const price = row.querySelector('[name="service_prices[]"]')?.value.trim() || '';
      const reviews = row.querySelector('[name="service_reviews[]"]')?.value.trim() || '';
      const imgEl = row.querySelector('.inline-img-preview');
      const imgSrc = (imgEl && !imgEl.classList.contains('hidden')) ? imgEl.src : '';
      const card = document.createElement('div');
      card.style.cssText = 'background:#fff;border:1px solid #f0f0f0;border-radius:12px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 1px 4px rgba(0,0,0,0.06);';
      const imgArea = document.createElement('div');
      imgArea.style.cssText = 'width:100%;aspect-ratio:1/1;background:#f7f7f7;overflow:hidden;display:flex;align-items:center;justify-content:center;';
      if (imgSrc) {
        const img = document.createElement('img');
        img.src = imgSrc;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
        imgArea.appendChild(img);
      } else { imgArea.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>'; }
      card.appendChild(imgArea);
      const info = document.createElement('div');
      info.style.cssText = 'padding:7px 8px 8px;flex:1;display:flex;flex-direction:column;gap:3px;';
      const nameEl = document.createElement('p');
      nameEl.style.cssText = 'font-size:10px;font-weight:700;color:#111;margin:0;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
      nameEl.textContent = name;
      info.appendChild(nameEl);
      const ratingRow = document.createElement('div');
      ratingRow.style.cssText = 'display:flex;align-items:center;gap:3px;margin:1px 0;';
      const reviewLabel = reviews ? reviews + ' reviews' : 'New';
      ratingRow.innerHTML = '<span style="color:#f59e0b;font-size:9px;">★</span><span style="font-size:8.5px;font-weight:600;color:#333;">4.5</span>' + `<span style="font-size:8px;color:#aaa;margin-left:1px;">· ${reviewLabel}</span>`;
      info.appendChild(ratingRow);
      const bottom = document.createElement('div');
      bottom.style.cssText = 'display:flex;align-items:center;justify-content:space-between;margin-top:4px;';
      const priceEl = document.createElement('span');
      priceEl.style.cssText = 'font-size:10px;font-weight:700;color:#111;';
      priceEl.textContent = price ? '₹' + price : '';
      bottom.appendChild(priceEl);
      const addBtn = document.createElement('button');
      addBtn.type = 'button';
      addBtn.style.cssText = 'font-size:9px;font-weight:600;color:#9333ea;border:1.5px solid #9333ea;border-radius:6px;padding:2px 8px;background:transparent;cursor:default;';
      addBtn.textContent = 'Add';
      bottom.appendChild(addBtn);
      info.appendChild(bottom);
      card.appendChild(info);
      grid.appendChild(card);
    });
    container.appendChild(grid);
  }

  // ── Hero image
  function pvUpdateHero(src) {
    const placeholder = document.getElementById('pv-hero-placeholder');
    const img = document.getElementById('pv-hero');
    if (!placeholder || !img) return;
    if (src) { img.src = src; img.classList.remove('hidden'); placeholder.style.display = 'none'; }
    else { img.classList.add('hidden'); placeholder.style.display = ''; }
  }

  // ── Description images
  function pvRebuildDescImgs() {
    const pv = document.getElementById('pv-desc-imgs');
    if (!pv) return;
    pv.innerHTML = '';
    document.querySelectorAll('#descImagesContainer .desc-preview').forEach(imgEl => {
      if (!imgEl.classList.contains('hidden') && imgEl.src) {
        const el = document.createElement('img');
        el.src = imgEl.src;
        el.className = 'preview-desc-img';
        el.style.cssText = 'width:100%;height:auto;display:block;';
        pv.appendChild(el);
      }
    });
  }

  // ── Event listeners
  document.querySelector('[name="name"]')?.addEventListener('input', pvUpdateName);
  document.querySelector('[name="category_id"]')?.addEventListener('change', pvUpdateCategory);
  document.querySelector('[name="subcategory"]')?.addEventListener('change', pvUpdateSubcategory);
  document.querySelector('[name="duration"]')?.addEventListener('change', pvUpdateDuration);
  document.querySelector('[name="description"]')?.addEventListener('input', pvUpdateDescription);
  document.querySelector('[name="desc_title"]')?.addEventListener('input', pvUpdateDescTitle);

  // Service variations — delegate input + watch DOM changes
  document.getElementById('serviceTypesContainer')?.addEventListener('input', pvUpdateServices);
  new MutationObserver(pvUpdateServices)
    .observe(document.getElementById('serviceTypesContainer') || document.body,
      { childList: true, subtree: true, attributes: true, attributeFilter: ['src','class'] });

  // Initial render
  pvUpdateName(); pvUpdateCategory(); pvUpdateSubcategory(); pvUpdateDuration();
  pvUpdateDescription(); pvUpdateDescTitle(); pvUpdateServices();
</script>
@endpush
