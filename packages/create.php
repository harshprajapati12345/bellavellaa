<?php
/**
 * packages/create.php — Create Package (Full UI)
 */
$pageTitle = 'Create Package';
$errors = [];
$success = false;
$categories = ['Bridal', 'Hair', 'Makeup', 'Nails', 'Skincare', 'Wellness'];

/* ── Mock services for selection ──────────────────────────────────────────── */
$availableServices = [
  ['id'=>1, 'name'=>'HD Bridal Makeup',       'duration'=>60, 'price'=>2999],
  ['id'=>2, 'name'=>'Hydra Facial',            'duration'=>45, 'price'=>1499],
  ['id'=>3, 'name'=>'Nail Art Deluxe',         'duration'=>40, 'price'=>799],
  ['id'=>4, 'name'=>'Aromatherapy Massage',    'duration'=>50, 'price'=>1999],
  ['id'=>5, 'name'=>'Classic Haircut',         'duration'=>30, 'price'=>599],
  ['id'=>6, 'name'=>'Gold Facial',             'duration'=>55, 'price'=>1799],
  ['id'=>7, 'name'=>'Party Glam Makeup',       'duration'=>45, 'price'=>1299],
  ['id'=>8, 'name'=>'Hair Spa Treatment',      'duration'=>50, 'price'=>999],
  ['id'=>9, 'name'=>'Manicure & Pedicure',     'duration'=>60, 'price'=>849],
  ['id'=>10,'name'=>'Deep Cleansing Facial',   'duration'=>40, 'price'=>1199],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $action   = $_POST['form_action'] ?? 'draft';

    if (!$name)      $errors[] = 'Package name is required.';
    if (!$category)  $errors[] = 'Category is required.';
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
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="/bellavella/assets/css/style.css">
  <style>
    .section-card { transition: box-shadow 0.25s ease; }
    .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .dyn-item { animation: slideUp 0.35s cubic-bezier(0.16,1,0.3,1); }
    @keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
    .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
    .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.85); }
    .svc-dropdown { position:absolute; top:100%; left:0; right:0; z-index:40; max-height:240px; overflow-y:auto; }
    .svc-dropdown-item { transition: background 0.12s; }
    .svc-dropdown-item:hover { background: #f3f4f6; }
    .selected-svc-row { animation: slideUp 0.3s cubic-bezier(0.16,1,0.3,1); }
    .selected-svc-row:hover { background: #fafafa; }
    .savings-badge { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
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
        <a href="/bellavella/packages/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Create Package</h2>
          <p class="text-sm text-gray-400 mt-0.5">Bundle multiple services into one package</p>
        </div>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Package created successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="packageForm">

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
                  <label class="form-label">Package Name <span class="text-red-400">*</span></label>
                  <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Bridal Glow Package" class="form-input" required>
                </div>
                <div>
                  <label class="form-label">Category <span class="text-red-400">*</span></label>
                  <select name="category" class="form-input cursor-pointer" required>
                    <option value="">Select category</option>
                    <?php foreach($categories as $cat): ?><option value="<?php echo $cat; ?>" <?php echo (($_POST['category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option><?php endforeach; ?>
                  </select>
                </div>
              </div>
              <!-- Right: Image Upload -->
              <div>
                <label class="form-label">Package Image <span class="text-red-400">*</span></label>
                <div id="dropZone1" class="drop-zone relative flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                     onclick="document.getElementById('packageImageInput').click()"
                     ondragover="event.preventDefault(); this.classList.add('dragover')"
                     ondragleave="this.classList.remove('dragover')"
                     ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'packageImageInput','packageImgPreview')">
                  <div id="uploadPlaceholder1" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mb-3 group-hover:bg-gray-200 transition-colors">
                      <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Click to upload or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 2MB</p>
                  </div>
                  <input type="file" name="package_image" id="packageImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'packageImgPreview','uploadPlaceholder1','removeBtn1')">
                </div>
                <div class="relative mt-3 hidden" id="packageImgPreviewWrap">
                  <img id="packageImgPreview" class="w-full h-48 object-cover rounded-2xl border border-gray-100" src="" alt="">
                  <button type="button" id="removeBtn1" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('packageImageInput','packageImgPreview','packageImgPreviewWrap','uploadPlaceholder1','dropZone1')">
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
              <label class="form-label">Select Service</label>
              <div class="flex items-center gap-3">
                <div class="relative flex-1">
                  <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                  <input type="text" id="serviceSearchInput" placeholder="Search services…" autocomplete="off"
                    class="form-input pl-10" onfocus="showServiceDropdown()" oninput="filterServiceDropdown()">
                  <!-- Dropdown -->
                  <div id="serviceDropdown" class="svc-dropdown hidden bg-white border border-gray-200 rounded-xl shadow-lg mt-1 overflow-hidden">
                    <?php foreach($availableServices as $svc): ?>
                    <div class="svc-dropdown-item px-4 py-3 cursor-pointer flex items-center justify-between"
                         data-id="<?php echo $svc['id']; ?>"
                         data-name="<?php echo htmlspecialchars($svc['name']); ?>"
                         data-duration="<?php echo $svc['duration']; ?>"
                         data-price="<?php echo $svc['price']; ?>"
                         onclick="selectService(this)">
                      <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo $svc['name']; ?></p>
                        <p class="text-xs text-gray-400 mt-0.5"><?php echo $svc['duration']; ?> min</p>
                      </div>
                      <span class="text-sm font-semibold text-gray-700">₹<?php echo number_format($svc['price']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div id="noResultsItem" class="hidden px-4 py-3 text-sm text-gray-400 text-center">No services found</div>
                  </div>
                </div>
                <button type="button" id="addServiceBtn" onclick="addSelectedService()" disabled
                  class="px-5 py-2.5 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap">
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
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                  <i data-lucide="package" class="w-6 h-6 text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">No services added yet</p>
                <p class="text-xs text-gray-400 mt-1">Search and add services to build your package</p>
              </div>
            </div>

            <!-- Hidden inputs for form submission -->
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
                <label class="form-label">Total Original Price</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">₹</span>
                  <input type="text" id="totalOriginalPrice" value="0" class="form-input pl-8 bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Sum of all included services</p>
              </div>
              <div>
                <label class="form-label">Total Duration</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">
                    <i data-lucide="clock" class="w-4 h-4 inline"></i>
                  </span>
                  <input type="text" id="totalDuration" value="0 min" class="form-input pl-10 bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Combined duration of all services</p>
              </div>
            </div>
            <div>
              <label class="form-label">Package Price <span class="text-red-400">*</span></label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-semibold">₹</span>
                <input type="number" name="package_price" id="packagePriceInput" value="" placeholder="0.00" min="0" step="0.01"
                  class="form-input pl-8 text-lg font-semibold" oninput="updateSavings()">
              </div>
              <p class="text-[11px] text-gray-400 mt-1.5 ml-1">The final price customers will pay</p>
            </div>

            <!-- Savings display -->
            <div id="savingsDisplay" class="hidden">
              <div class="savings-badge rounded-2xl border border-emerald-200 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i data-lucide="trending-down" class="w-5 h-5 text-emerald-600"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-emerald-800">Customer Savings</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Discount applied to package</p>
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
              <label class="form-label">Description Title</label>
              <input type="text" name="desc_title" value="<?php echo htmlspecialchars($_POST['desc_title'] ?? ''); ?>" placeholder="e.g. Complete Bridal Glow Experience" class="form-input">
            </div>
            <div>
              <label class="form-label">Description Content</label>
              <!-- Rich-text toolbar -->
              <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-1 px-3 py-2 bg-gray-50/80 border-b border-gray-200">
                  <button type="button" onclick="execCmd('bold')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bold"><b class="text-sm text-gray-600">B</b></button>
                  <button type="button" onclick="execCmd('italic')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Italic"><i class="text-sm text-gray-600" style="font-style:italic">I</i></button>
                  <button type="button" onclick="execCmd('underline')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Underline"><u class="text-sm text-gray-600">U</u></button>
                  <div class="w-px h-5 bg-gray-200 mx-1"></div>
                  <button type="button" onclick="execCmd('insertUnorderedList')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bullet List"><i data-lucide="list" class="w-4 h-4 text-gray-500"></i></button>
                  <button type="button" onclick="execCmd('insertOrderedList')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Numbered List"><i data-lucide="list-ordered" class="w-4 h-4 text-gray-500"></i></button>
                  <div class="w-px h-5 bg-gray-200 mx-1"></div>
                  <button type="button" onclick="execCmd('justifyLeft')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Align Left"><i data-lucide="align-left" class="w-4 h-4 text-gray-500"></i></button>
                  <button type="button" onclick="execCmd('justifyCenter')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Align Center"><i data-lucide="align-center" class="w-4 h-4 text-gray-500"></i></button>
                </div>
                <div id="descEditor" contenteditable="true" class="px-4 py-3 min-h-[160px] text-sm text-gray-800 outline-none focus:ring-0" style="font-family:Inter,sans-serif" data-placeholder="Describe this package and what's included…"></div>
                <input type="hidden" name="desc_content" id="descContentHidden">
              </div>
            </div>
            <div>
              <label class="form-label">Description Image <span class="text-gray-400 font-normal text-[10px] tracking-normal normal-case">(Optional)</span></label>
              <div id="dropZoneDesc" class="drop-zone flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                   onclick="document.getElementById('descImageInput').click()"
                   ondragover="event.preventDefault(); this.classList.add('dragover')"
                   ondragleave="this.classList.remove('dragover')"
                   ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'descImageInput','descImgPreview')">
                <div id="uploadPlaceholderDesc" class="flex flex-col items-center">
                  <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors">
                    <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
                  </div>
                  <p class="text-sm text-gray-500">Upload description image</p>
                  <p class="text-xs text-gray-400 mt-0.5">Optional</p>
                </div>
                <input type="file" name="desc_image" id="descImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'descImgPreview','uploadPlaceholderDesc','removeBtnDesc')">
              </div>
              <div class="relative mt-3 hidden" id="descImgPreviewWrap">
                <img id="descImgPreview" class="w-full h-40 object-cover rounded-2xl border border-gray-100" src="" alt="">
                <button type="button" id="removeBtnDesc" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('descImageInput','descImgPreview','descImgPreviewWrap','uploadPlaceholderDesc','dropZoneDesc')">
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
              <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider bg-gray-100 px-2 py-0.5 rounded-md">Optional</span>
            </div>
          </div>
          <div class="px-8 pb-8 space-y-5">
            <div>
              <label class="form-label">Aftercare Content</label>
              <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-1 px-3 py-2 bg-gray-50/80 border-b border-gray-200">
                  <button type="button" onclick="execCmdAfter('bold')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bold"><b class="text-sm text-gray-600">B</b></button>
                  <button type="button" onclick="execCmdAfter('italic')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Italic"><i class="text-sm text-gray-600" style="font-style:italic">I</i></button>
                  <button type="button" onclick="execCmdAfter('underline')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Underline"><u class="text-sm text-gray-600">U</u></button>
                  <div class="w-px h-5 bg-gray-200 mx-1"></div>
                  <button type="button" onclick="execCmdAfter('insertUnorderedList')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Bullet List"><i data-lucide="list" class="w-4 h-4 text-gray-500"></i></button>
                  <button type="button" onclick="execCmdAfter('insertOrderedList')" class="w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center transition-colors" title="Numbered List"><i data-lucide="list-ordered" class="w-4 h-4 text-gray-500"></i></button>
                </div>
                <div id="aftercareEditor" contenteditable="true" class="px-4 py-3 min-h-[120px] text-sm text-gray-800 outline-none focus:ring-0" style="font-family:Inter,sans-serif" data-placeholder="Post-service care instructions…"></div>
                <input type="hidden" name="aftercare_content" id="aftercareContentHidden">
              </div>
            </div>
            <div>
              <label class="form-label">Aftercare Image <span class="text-gray-400 font-normal text-[10px] tracking-normal normal-case">(Optional)</span></label>
              <div id="dropZoneAfter" class="drop-zone flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                   onclick="document.getElementById('afterImageInput').click()"
                   ondragover="event.preventDefault(); this.classList.add('dragover')"
                   ondragleave="this.classList.remove('dragover')"
                   ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'afterImageInput','afterImgPreview')">
                <div id="uploadPlaceholderAfter" class="flex flex-col items-center">
                  <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors">
                    <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
                  </div>
                  <p class="text-sm text-gray-500">Upload image</p>
                </div>
                <input type="file" name="aftercare_image" id="afterImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'afterImgPreview','uploadPlaceholderAfter','removeBtnAfter')">
              </div>
              <div class="relative mt-3 hidden" id="afterImgPreviewWrap">
                <img id="afterImgPreview" class="w-full h-36 object-cover rounded-2xl border border-gray-100" src="" alt="">
                <button type="button" id="removeBtnAfter" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('afterImageInput','afterImgPreview','afterImgPreviewWrap','uploadPlaceholderAfter','dropZoneAfter')">
                  <i data-lucide="x" class="w-4 h-4"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ━━━ STICKY ACTION BAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2">
          <a href="/bellavella/packages/" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="form_action" value="draft" class="btn btn-secondary">
            <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
          </button>
          <button type="submit" name="form_action" value="publish" class="btn btn-primary" onclick="document.getElementById('descContentHidden').value = document.getElementById('descEditor').innerHTML; document.getElementById('aftercareContentHidden').value = document.getElementById('aftercareEditor').innerHTML;">
            <i data-lucide="globe" class="w-4 h-4"></i> Publish Package
          </button>
        </div>

      </form>
    </div>
  </main>
</div>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function reInitIcons() { lucide.createIcons({ attrs: { 'stroke-width': 1.5 } }); }

  // ── Sidebar toggles ──
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  // ── Image Upload helpers ──
  function previewImg(input, previewId, placeholderId, removeBtnId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      const preview = document.getElementById(previewId);
      preview.src = e.target.result;
      const wrap = preview.closest('[id$="PreviewWrap"]') || preview.parentElement;
      wrap.classList.remove('hidden');
      document.getElementById(placeholderId).classList.add('hidden');
      const dz = input.closest('.drop-zone');
      if (dz) dz.classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function removeImage(inputId, previewId, wrapId, placeholderId, dropZoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(wrapId).classList.add('hidden');
    document.getElementById(placeholderId).classList.remove('hidden');
    document.getElementById(dropZoneId).classList.remove('hidden');
  }

  function handleDrop(e, inputId, previewId) {
    const dt = e.dataTransfer;
    if (dt.files && dt.files[0]) {
      document.getElementById(inputId).files = dt.files;
      document.getElementById(inputId).dispatchEvent(new Event('change'));
    }
  }

  // ── Rich Text Editors ──
  function execCmd(cmd, val = null) {
    document.getElementById('descEditor').focus();
    document.execCommand(cmd, false, val);
  }
  function execCmdAfter(cmd, val = null) {
    document.getElementById('aftercareEditor').focus();
    document.execCommand(cmd, false, val);
  }

  // ── Placeholder for contenteditable ──
  document.querySelectorAll('[contenteditable][data-placeholder]').forEach(el => {
    const check = () => { el.classList.toggle('empty', el.textContent.trim() === ''); };
    el.addEventListener('input', check); el.addEventListener('focus', check); el.addEventListener('blur', check);
    check();
  });

  // ── Service Selection Logic ──
  const allServices = <?php echo json_encode($availableServices); ?>;
  let selectedServices = [];
  let pendingService = null;

  function showServiceDropdown() {
    document.getElementById('serviceDropdown').classList.remove('hidden');
    filterServiceDropdown();
  }

  function filterServiceDropdown() {
    const q = document.getElementById('serviceSearchInput').value.toLowerCase();
    const dropdown = document.getElementById('serviceDropdown');
    const items = dropdown.querySelectorAll('.svc-dropdown-item');
    let visible = 0;
    items.forEach(item => {
      const name = item.dataset.name.toLowerCase();
      const id = parseInt(item.dataset.id);
      const alreadyAdded = selectedServices.some(s => s.id === id);
      if (name.includes(q) && !alreadyAdded) {
        item.style.display = '';
        visible++;
      } else {
        item.style.display = 'none';
      }
    });
    document.getElementById('noResultsItem').style.display = visible === 0 ? '' : 'none';
  }

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
    if (selectedServices.some(s => s.id === pendingService.id)) return;
    selectedServices.push({ ...pendingService });
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
      // Hidden inputs
      hidden.innerHTML += `<input type="hidden" name="service_ids[]" value="${svc.id}">`;

      const row = document.createElement('div');
      row.className = 'selected-svc-row dyn-item flex items-center gap-3 px-4 py-3.5 border border-gray-100 rounded-2xl transition-all';
      row.innerHTML = `
        <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
          <span class="text-xs font-bold text-gray-500">${idx + 1}</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-900 truncate">${svc.name}</p>
        </div>
        <div class="w-24 text-center">
          <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            ${svc.duration} min
          </span>
        </div>
        <div class="w-28 text-right">
          <span class="text-sm font-semibold text-gray-700">₹${svc.price.toLocaleString('en-IN')}</span>
        </div>
        <button type="button" onclick="removeSelectedService(${svc.id})"
          class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center flex-shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        </button>
      `;
      list.appendChild(row);
    });
  }

  function recalcTotals() {
    const totalPrice = selectedServices.reduce((sum, s) => sum + s.price, 0);
    const totalDur = selectedServices.reduce((sum, s) => sum + s.duration, 0);

    document.getElementById('totalOriginalPrice').value = totalPrice.toLocaleString('en-IN');
    const hrs = Math.floor(totalDur / 60);
    const mins = totalDur % 60;
    document.getElementById('totalDuration').value = hrs > 0 ? `${hrs} hr ${mins} min` : `${totalDur} min`;

    updateSavings();
  }

  function updateSavings() {
    const totalPrice = selectedServices.reduce((sum, s) => sum + s.price, 0);
    const pkgPrice = parseFloat(document.getElementById('packagePriceInput').value) || 0;
    const savingsEl = document.getElementById('savingsDisplay');

    if (pkgPrice > 0 && totalPrice > 0 && pkgPrice < totalPrice) {
      const saved = totalPrice - pkgPrice;
      const pct = Math.round((saved / totalPrice) * 100);
      document.getElementById('savingsAmount').textContent = `₹${saved.toLocaleString('en-IN')}`;
      document.getElementById('savingsPercent').textContent = `${pct}% off`;
      savingsEl.classList.remove('hidden');
    } else {
      savingsEl.classList.add('hidden');
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('serviceDropdown');
    const input = document.getElementById('serviceSearchInput');
    if (!dropdown.contains(e.target) && e.target !== input) {
      dropdown.classList.add('hidden');
    }
  });
</script>
</body>
</html>
