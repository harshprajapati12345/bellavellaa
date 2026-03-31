<?php
/**
 * services/edit.php — Edit Service (Full UI)
 */
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/services/'); exit; }

// -- Sample data (would come from DB) -------------------------------------
$service = [
    'id'            => $id,
    'name'          => 'Bridal Makeup',
    'category'      => 'Bridal',
    'duration'      => 40,
    'desc_title'    => 'Premium Bridal Makeup Experience',
    'status'        => 'active',
    'image'         => '',
    'service_types' => [
        ['name' => 'Full Face Bridal', 'price' => 4500, 'desc' => ''],
    ],
];

$pageTitle = 'Edit Service';
$errors = [];
$success = false;
$categories = ['Bridal','Hair','Makeup','Nails','Skincare','Wellness'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service['name']             = trim($_POST['name'] ?? '');
    $service['category']         = trim($_POST['category'] ?? '');
    $service['price']            = floatval($_POST['price'] ?? 0);
    $service['duration']         = intval($_POST['duration'] ?? 0);
    $service['service_types']    = array_filter($_POST['service_types'] ?? []);
    $formAction = $_POST['form_action'] ?? 'draft';

    if (!$service['name'])     $errors[] = 'Name required.';
    if (!$service['category']) $errors[] = 'Category required.';
    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?> · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    .section-card { transition: box-shadow 0.25s ease; }
    .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .dyn-item { animation: slideUp 0.35s cubic-bezier(0.16,1,0.3,1); }
    @keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
    .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
    .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.85); }
  </style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto pb-28">
    <?php include '../includes/header.php'; ?>
    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex items-center gap-4">
        <a href="/bella/services/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Service</h2>
          <p class="text-sm text-gray-400 mt-0.5"><?php echo htmlspecialchars($service['name']); ?></p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $service['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-100 text-gray-600'; ?>"><?php echo ucfirst($service['status']); ?></span>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Service updated successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="serviceForm">

        <!-- ??? SECTION 1 · BASIC DETAILS ????????????????????????????????? -->
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
              <div class="space-y-5">
                <div>
                  <label class="form-label">Service Name <span class="text-red-400">*</span></label>
                  <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" class="form-input" required>
                </div>
                <div>
                  <label class="form-label">Category <span class="text-red-400">*</span></label>
                  <select name="category" class="form-input cursor-pointer" required>
                    <option value="">Select category</option>
                    <?php foreach($categories as $cat): ?><option value="<?php echo $cat; ?>" <?php echo ($service['category'] === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option><?php endforeach; ?>
                  </select>
                </div>
              </div>
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
          </div>
        </div>

        <!-- ??? SECTION 2 · SERVICE PREVIEW ??????????????????????????????? -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Service Preview</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8 space-y-6">

            <!-- 1. Duration (Global) -->
            <div>
              <label class="form-label">Duration <span class="text-red-400">*</span></label>
              <select name="duration" class="form-input cursor-pointer md:w-1/2" required>
                <option value="">Select Duration</option>
                <?php for($d = 10; $d <= 60; $d += 10): ?>
                <option value="<?php echo $d; ?>" <?php echo ($service['duration'] === $d) ? 'selected' : ''; ?>><?php echo $d; ?> min</option>
                <?php endfor; ?>
              </select>
              <p class="text-[11px] text-gray-400 mt-1.5 ml-1">This duration applies to all service variations</p>
            </div>

            <!-- 2. Service Variations (Dynamic) -->
            <div>
              <label class="form-label">Service Types</label>
              <div id="serviceTypesContainer" class="space-y-4">
                <?php foreach ($service['service_types'] as $idx => $type): ?>
                <div class="service-type-row dyn-item rounded-2xl border border-gray-200 p-5">
                  <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                      <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                      <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider svc-label">Service <?php echo $idx + 1; ?></span>
                    </div>
                    <button type="button" onclick="removeServiceCard(this)" class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Service Name</label>
                      <input type="text" name="service_types[]" value="<?php echo htmlspecialchars($type['name']); ?>" class="form-input">
                    </div>
                    <div>
                      <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (?) *</label>
                      <input type="number" name="service_prices[]" value="<?php echo $type['price']; ?>" min="0" step="0.01" class="form-input text-right svc-price">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Description</label>
                    <textarea name="service_descs[]" rows="2" placeholder="Brief description of this service type..." class="form-input resize-none"><?php echo htmlspecialchars($type['desc'] ?? ''); ?></textarea>
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
                <?php endforeach; ?>
              </div>
              <button type="button" onclick="addServiceType()" class="mt-4 flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors group">
                <div class="w-7 h-7 rounded-lg border border-dashed border-gray-300 flex items-center justify-center group-hover:border-black transition-colors"><i data-lucide="plus" class="w-3.5 h-3.5"></i></div> Add Another Service
              </button>
            </div>


          </div>
        </div>


        <!-- ??? SECTION 3 · SERVICE DESCRIPTION ??????????????????????????? -->
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
              <input type="text" name="desc_title" value="<?php echo htmlspecialchars($service['desc_title']); ?>" class="form-input">
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

        <!-- ??? STICKY ACTION BAR ????????????????????????????????????????? -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-between mt-2">
          <a href="/bella/services/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this service?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete</a>
          <div class="flex gap-3">
            <a href="/bella/services/" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="form_action" value="draft" class="btn btn-secondary"><i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft</button>
            <button type="submit" name="form_action" value="publish" class="btn btn-primary"><i data-lucide="globe" class="w-4 h-4"></i> Publish Service</button>
          </div>
        </div>

      </form>

    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  /* -- Image helpers ---------------------------------------------------- */
  function previewImg(input, previewId, placeholderId, removeBtnId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById(previewId);
      img.src = e.target.result;
      const wrap = img.closest('[id$="PreviewWrap"]') || img.parentElement;
      wrap.classList.remove('hidden');
      document.getElementById(placeholderId).classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }
  function removeImage(inputId, previewId, wrapId, placeholderId, dropZoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(wrapId).classList.add('hidden');
    document.getElementById(placeholderId).classList.remove('hidden');
  }
  function handleDrop(e, inputId, previewId) {
    const dt = e.dataTransfer;
    if (dt.files && dt.files[0]) { const input = document.getElementById(inputId); input.files = dt.files; input.dispatchEvent(new Event('change')); }
  }
  function previewInlineImg(input) {
    const preview = input.closest('.inline-upload-wrap').querySelector('.inline-img-preview');
    const placeholder = input.closest('.inline-upload-wrap').querySelector('.inline-upload-placeholder');
    const removeBtn = input.closest('.inline-upload-wrap').querySelector('.inline-remove-btn');
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); placeholder.classList.add('hidden'); if (removeBtn) removeBtn.classList.remove('hidden'); };
    reader.readAsDataURL(input.files[0]);
  }
  function removeInlineImg(btn) {
    const wrap = btn.closest('.inline-upload-wrap');
    wrap.querySelector('input[type="file"]').value = '';
    wrap.querySelector('.inline-img-preview').classList.add('hidden');
    wrap.querySelector('.inline-upload-placeholder').classList.remove('hidden');
    btn.classList.add('hidden');
  }

  /* -- Service Variations (card-based with name, price, description, image) -- */
  let svcCount = document.querySelectorAll('.service-type-row').length;

  function addServiceType() {
    svcCount++;
    const c = document.getElementById('serviceTypesContainer'), d = document.createElement('div');
    d.className = 'service-type-row dyn-item rounded-2xl border border-gray-200 p-5';
    d.innerHTML = `
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
          <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (?) *</label>
          <input type="number" name="service_prices[]" placeholder="0.00" min="0" step="0.01" class="form-input text-right svc-price">
        </div>
      </div>
      <div class="mb-4">
        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Description</label>
        <textarea name="service_descs[]" rows="2" placeholder="Brief description of this service type..." class="form-input resize-none"></textarea>
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
    c.appendChild(d);
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

  /* -------------------------------------------------------------------------
     DESCRIPTION IMAGES (multi-slot grid)
     ------------------------------------------------------------------------- */

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
    const container = document.getElementById('descImagesContainer');
    container.appendChild(buildDescSlot());
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
    updateDescRemoveButtons();
  }

  function previewDescImg(input) {
    const slot      = input.closest('.desc-img-slot');
    const label     = slot.querySelector('.desc-upload-label');
    const preview   = slot.querySelector('.desc-preview');
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

</script>
</body>
</html>
