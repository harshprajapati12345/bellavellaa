<?php
/**
 * services/edit.php — Edit Service (Full UI)
 */
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/services/'); exit; }

// ── Sample data (would come from DB) ─────────────────────────────────────
$service = [
    'id'            => $id,
    'name'          => 'Bridal Makeup',
    'category'      => 'Bridal',
    'duration'      => 40,
    'rating'        => 5,
    'service_types' => [
        ['name' => 'Classic Bridal',       'price' => 2999, 'desc' => 'Traditional bridal makeup with premium products for a timeless look.'],
        ['name' => 'HD Airbrush Bridal',   'price' => 4999, 'desc' => 'High-definition airbrush technique for flawless, camera-ready finish.'],
        ['name' => 'Reception Look',       'price' => 1999, 'desc' => 'Elegant evening reception makeup designed to last all night.'],
    ],
    'desc_title'    => 'Premium Bridal Makeup Experience',
    'desc_content'  => '<p>Our signature bridal makeup service uses premium HD products and airbrush techniques to give you a flawless, long-lasting look for your special day.</p><p>Each session is personalized to match your outfit and jewelry.</p>',
    'steps' => [
        ['title'=>'Skin Preparation','description'=>'Deep cleansing and moisturizing to create the perfect canvas for makeup application.'],
        ['title'=>'Foundation & Base','description'=>'Airbrush foundation matched to your skin tone for a flawless, camera-ready base.'],
        ['title'=>'Eyes & Lips','description'=>'Custom eye makeup and lip color coordinated with your bridal outfit.'],
    ],
    'trusted' => [
        ['title'=>'Why Choose Our Bridal Service','points'=>"Premium imported products\nCertified makeup artists\n12+ hours long-lasting finish"],
    ],
    'aftercare_title'   => 'Post-Bridal Makeup Care',
    'aftercare_content' => "• Avoid touching your face for the first 2 hours.\n• Use blotting papers instead of powder for touch-ups.\n• Remove makeup gently with micellar water at end of day.\n• Apply moisturizer after removal to hydrate skin.",
    'status'  => 'active',
    'image'   => '',
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
    $service['desc_title']       = trim($_POST['desc_title'] ?? '');
    $service['desc_content']     = $_POST['desc_content'] ?? '';
    $service['aftercare_title']  = trim($_POST['aftercare_title'] ?? '');
    $service['aftercare_content']= trim($_POST['aftercare_content'] ?? '');
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
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="/bellavella/assets/css/style.css">
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
        <a href="/bellavella/services/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Service</h2>
          <p class="text-sm text-gray-400 mt-0.5"><?php echo htmlspecialchars($service['name']); ?></p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $service['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-100 text-gray-600'; ?>"><?php echo ucfirst($service['status']); ?></span>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Service updated successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="serviceForm">

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
                      <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (₹) *</label>
                      <input type="number" name="service_prices[]" value="<?php echo $type['price']; ?>" min="0" step="0.01" class="form-input text-right svc-price" oninput="updateDisplayPrice()">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Description</label>
                    <textarea name="service_descs[]" rows="2" placeholder="Brief description of this service type…" class="form-input resize-none"><?php echo htmlspecialchars($type['desc'] ?? ''); ?></textarea>
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

            <!-- 3. Display Price (Auto-calculated) -->
            <div>
              <label class="form-label">Display Price</label>
              <div class="flex items-center justify-between py-4 px-5 bg-gray-50/80 rounded-2xl border border-gray-100">
                <div>
                  <p class="text-sm font-medium text-gray-700" id="displayPriceLabel">Auto-calculated</p>
                  <p class="text-[11px] text-gray-400 mt-0.5">Based on lowest service price</p>
                </div>
                <span class="text-sm font-semibold text-gray-800 bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm" id="displayPricePreview">₹ —</span>
              </div>
            </div>

            <!-- 4. Rating (Global) -->
            <div>
              <label class="form-label">Rating</label>
              <div class="flex items-center gap-1 mt-1" id="starRating">
                <?php for($s = 1; $s <= 5; $s++): ?>
                <button type="button" onclick="setRating(<?php echo $s; ?>)" class="star-btn p-1 transition-transform hover:scale-110" data-star="<?php echo $s; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </button>
                <?php endfor; ?>
                <input type="hidden" name="rating" id="ratingValue" value="<?php echo $service['rating']; ?>">
                <span class="ml-3 text-sm font-semibold text-gray-700" id="ratingText"><?php echo $service['rating']; ?>.0</span>
              </div>
              <p class="text-[11px] text-gray-400 mt-1.5 ml-1">This rating applies to the entire service</p>
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
              <input type="text" name="desc_title" value="<?php echo htmlspecialchars($service['desc_title']); ?>" class="form-input">
            </div>
            <div>
              <label class="form-label">Description Content</label>
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
                <div id="descEditor" contenteditable="true" class="px-4 py-3 min-h-[160px] text-sm text-gray-800 outline-none focus:ring-0" style="font-family:Inter,sans-serif" data-placeholder="Provide a detailed overview of this service…"><?php echo $service['desc_content']; ?></div>
                <input type="hidden" name="desc_content" id="descContentHidden">
              </div>
            </div>
            <div>
              <label class="form-label">Description Image</label>
              <div id="dropZoneDesc" class="drop-zone flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                   onclick="document.getElementById('descImageInput').click()"
                   ondragover="event.preventDefault(); this.classList.add('dragover')"
                   ondragleave="this.classList.remove('dragover')"
                   ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'descImageInput','descImgPreview')">
                <div id="uploadPlaceholderDesc" class="flex flex-col items-center">
                  <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors"><i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i></div>
                  <p class="text-sm text-gray-500">Upload description image</p>
                  <p class="text-xs text-gray-400 mt-0.5">Optional</p>
                </div>
                <input type="file" name="desc_image" id="descImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'descImgPreview','uploadPlaceholderDesc','removeBtnDesc')">
              </div>
              <div class="relative mt-3 hidden" id="descImgPreviewWrap">
                <img id="descImgPreview" class="w-full h-40 object-cover rounded-2xl border border-gray-100" src="" alt="">
                <button type="button" id="removeBtnDesc" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('descImageInput','descImgPreview','descImgPreviewWrap','uploadPlaceholderDesc','dropZoneDesc')"><i data-lucide="x" class="w-4 h-4"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- ━━━ SECTION 4 · SERVICE STEPS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">4</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Service Steps</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div id="stepsContainer" class="space-y-4"></div>
            <button type="button" onclick="addStep()" class="mt-5 flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors group">
              <div class="w-7 h-7 rounded-lg border border-dashed border-gray-300 flex items-center justify-center group-hover:border-black transition-colors"><i data-lucide="plus" class="w-3.5 h-3.5"></i></div> Add New Step
            </button>
          </div>
        </div>

        <!-- ━━━ SECTION 5 · TRUSTED DESCRIPTION ━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">5</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Trusted Description</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div id="trustedContainer" class="space-y-4"></div>
            <button type="button" onclick="addTrustedBlock()" class="mt-5 flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors group">
              <div class="w-7 h-7 rounded-lg border border-dashed border-gray-300 flex items-center justify-center group-hover:border-black transition-colors"><i data-lucide="plus" class="w-3.5 h-3.5"></i></div> Add More
            </button>
          </div>
        </div>

        <!-- ━━━ SECTION 6 · AFTERCARE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6 section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">6</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Aftercare</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8 space-y-5">
            <div>
              <label class="form-label">Aftercare Title</label>
              <input type="text" name="aftercare_title" value="<?php echo htmlspecialchars($service['aftercare_title']); ?>" class="form-input">
            </div>
            <div>
              <label class="form-label">Aftercare Content</label>
              <textarea name="aftercare_content" rows="5" class="form-input resize-none"><?php echo htmlspecialchars($service['aftercare_content']); ?></textarea>
            </div>
            <div>
              <label class="form-label">Aftercare Image <span class="text-gray-400 font-normal text-[10px] tracking-normal normal-case">(Optional)</span></label>
              <div id="dropZoneAfter" class="drop-zone flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                   onclick="document.getElementById('afterImageInput').click()"
                   ondragover="event.preventDefault(); this.classList.add('dragover')"
                   ondragleave="this.classList.remove('dragover')"
                   ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event,'afterImageInput','afterImgPreview')">
                <div id="uploadPlaceholderAfter" class="flex flex-col items-center">
                  <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-gray-200 transition-colors"><i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i></div>
                  <p class="text-sm text-gray-500">Upload image</p>
                </div>
                <input type="file" name="aftercare_image" id="afterImageInput" accept="image/*" class="hidden" onchange="previewImg(this,'afterImgPreview','uploadPlaceholderAfter','removeBtnAfter')">
              </div>
              <div class="relative mt-3 hidden" id="afterImgPreviewWrap">
                <img id="afterImgPreview" class="w-full h-36 object-cover rounded-2xl border border-gray-100" src="" alt="">
                <button type="button" id="removeBtnAfter" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm" onclick="removeImage('afterImageInput','afterImgPreview','afterImgPreviewWrap','uploadPlaceholderAfter','dropZoneAfter')"><i data-lucide="x" class="w-4 h-4"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- ━━━ STICKY ACTION BAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-between mt-2">
          <a href="/bellavella/services/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this service?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete</a>
          <div class="flex gap-3">
            <a href="/bellavella/services/" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="form_action" value="draft" class="btn btn-secondary"><i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft</button>
            <button type="submit" name="form_action" value="publish" class="btn btn-primary" onclick="document.getElementById('descContentHidden').value = document.getElementById('descEditor').innerHTML;"><i data-lucide="globe" class="w-4 h-4"></i> Publish Service</button>
          </div>
        </div>

      </form>

    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  /* ── Image helpers ──────────────────────────────────────────────────── */
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

  /* ── Service Variations (card-based with name, price, description, image) ── */
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
          <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Price (₹) *</label>
          <input type="number" name="service_prices[]" placeholder="0.00" min="0" step="0.01" class="form-input text-right svc-price" oninput="updateDisplayPrice()">
        </div>
      </div>
      <div class="mb-4">
        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1 block">Description</label>
        <textarea name="service_descs[]" rows="2" placeholder="Brief description of this service type…" class="form-input resize-none"></textarea>
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
    c.appendChild(d); updateDisplayPrice();
  }

  function removeServiceCard(btn) {
    const rows = document.querySelectorAll('#serviceTypesContainer .service-type-row');
    if (rows.length > 1) {
      btn.closest('.service-type-row').remove();
      renumberServices();
      updateDisplayPrice();
    }
  }

  function renumberServices() {
    document.querySelectorAll('#serviceTypesContainer .service-type-row').forEach((card, i) => {
      const label = card.querySelector('.svc-label');
      if (label) label.textContent = `Service ${i + 1}`;
    });
    svcCount = document.querySelectorAll('#serviceTypesContainer .service-type-row').length;
  }

  /* ── Star Rating ────────────────────────────────────────────────────── */
  function setRating(val) {
    document.getElementById('ratingValue').value = val;
    document.getElementById('ratingText').textContent = val + '.0';
    document.querySelectorAll('#starRating .star-btn svg').forEach((svg, i) => {
      svg.setAttribute('fill', i < val ? '#f59e0b' : 'none');
      svg.setAttribute('stroke', i < val ? '#f59e0b' : '#d1d5db');
    });
  }
  setRating(<?php echo intval($service['rating']); ?>);

  /* ── Display Price (auto-calc from lowest variation price) ───────── */
  const displayPrice = document.getElementById('displayPricePreview');
  const displayLabel = document.getElementById('displayPriceLabel');
  function updateDisplayPrice() {
    const priceInputs = document.querySelectorAll('.svc-price');
    const prices = [];
    priceInputs.forEach(inp => { const v = parseFloat(inp.value); if (v > 0) prices.push(v); });
    if (prices.length === 0) { displayPrice.textContent = '₹ —'; displayLabel.textContent = 'Auto-calculated'; return; }
    const lowest = Math.min(...prices);
    displayPrice.textContent = prices.length > 1 ? `Starting From ₹${lowest.toLocaleString('en-IN')}` : `₹${lowest.toLocaleString('en-IN')}`;
    displayLabel.textContent = 'Auto-calculated';
  }
  updateDisplayPrice();
  new MutationObserver(updateDisplayPrice).observe(document.getElementById('serviceTypesContainer'), { childList: true, subtree: true });

  /* ── Rich Text Editor ───────────────────────────────────────────────── */
  function execCmd(cmd) { document.execCommand(cmd, false, null); }
  const es = document.createElement('style'); es.textContent = `#descEditor:empty:before{content:attr(data-placeholder);color:#9ca3af;pointer-events:none;}`; document.head.appendChild(es);
  document.getElementById('serviceForm').addEventListener('submit', () => { document.getElementById('descContentHidden').value = document.getElementById('descEditor').innerHTML; });

  /* ── Service Steps ──────────────────────────────────────────────────── */
  let stepCount = 0;
  function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
  function addStep(title='', desc='') {
    stepCount++;
    const c = document.getElementById('stepsContainer'), d = document.createElement('div');
    d.className = 'dyn-item rounded-2xl border border-gray-200 p-5 relative step-block';
    d.innerHTML = `<div class="flex items-center justify-between mb-4"><div class="flex items-center gap-2.5"><span class="w-7 h-7 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold step-num">${stepCount}</span><span class="text-xs font-semibold text-gray-500 uppercase tracking-wider step-label">Step ${stepCount}</span></div><button type="button" onclick="removeStep(this)" class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div class="space-y-4"><div><label class="form-label">Step Title</label><input type="text" name="step_titles[]" value="${escapeHtml(title)}" class="form-input"></div><div><label class="form-label">Step Description</label><textarea name="step_descriptions[]" rows="3" class="form-input resize-none">${escapeHtml(desc)}</textarea></div></div><div class="inline-upload-wrap"><label class="form-label">Step Image</label><label class="inline-upload-placeholder flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg><p class="text-xs text-gray-400 mt-2">Upload step image</p><input type="file" name="step_images[]" accept="image/*" class="hidden" onchange="previewInlineImg(this)"></label><div class="relative mt-2"><img class="inline-img-preview hidden w-full h-32 object-cover rounded-xl border border-gray-100" src="" alt=""><button type="button" class="inline-remove-btn hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm" onclick="removeInlineImg(this)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button></div></div></div>`;
    c.appendChild(d);
  }
  function removeStep(btn) { btn.closest('.step-block').remove(); renumberSteps(); }
  function renumberSteps() { document.querySelectorAll('#stepsContainer .step-block').forEach((b,i)=>{ b.querySelector('.step-num').textContent=i+1; b.querySelector('.step-label').textContent=`Step ${i+1}`; }); stepCount=document.querySelectorAll('#stepsContainer .step-block').length; }

  /* ── Trusted Description ────────────────────────────────────────────── */
  let trustedCount = 0;
  function addTrustedBlock(title='', points='') {
    trustedCount++;
    const c = document.getElementById('trustedContainer'), d = document.createElement('div');
    d.className = 'dyn-item rounded-2xl border border-gray-200 p-5 relative trusted-block';
    d.innerHTML = `<div class="flex items-center justify-between mb-4"><div class="flex items-center gap-2.5"><div class="w-2.5 h-2.5 rounded-full bg-gray-900"></div><span class="text-xs font-semibold text-gray-500 uppercase tracking-wider trusted-label">Block ${trustedCount}</span></div><button type="button" onclick="removeTrusted(this)" class="w-8 h-8 rounded-xl border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button></div><div class="space-y-4"><div><label class="form-label">Trusted Title</label><input type="text" name="trusted_titles[]" value="${escapeHtml(title)}" class="form-input"></div><div><label class="form-label">Key Points</label><textarea name="trusted_points[]" rows="3" class="form-input resize-none" placeholder="Enter key points (one per line)…">${escapeHtml(points)}</textarea></div><div class="inline-upload-wrap"><label class="form-label">Image</label><label class="inline-upload-placeholder flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg><p class="text-xs text-gray-400 mt-1.5">Upload image</p><input type="file" name="trusted_images[]" accept="image/*" class="hidden" onchange="previewInlineImg(this)"></label><div class="relative mt-2"><img class="inline-img-preview hidden w-full h-28 object-cover rounded-xl border border-gray-100" src="" alt=""><button type="button" class="inline-remove-btn hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm" onclick="removeInlineImg(this)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button></div></div></div>`;
    c.appendChild(d);
  }
  function removeTrusted(btn) { btn.closest('.trusted-block').remove(); renumberTrusted(); }
  function renumberTrusted() { document.querySelectorAll('#trustedContainer .trusted-block').forEach((b,i)=>{ b.querySelector('.trusted-label').textContent=`Block ${i+1}`; }); trustedCount=document.querySelectorAll('#trustedContainer .trusted-block').length; }

  /* ── Pre-fill from PHP ──────────────────────────────────────────────── */
  <?php foreach ($service['steps'] as $step): ?>
  addStep(<?php echo json_encode($step['title']); ?>, <?php echo json_encode($step['description']); ?>);
  <?php endforeach; ?>

  <?php foreach ($service['trusted'] as $tb): ?>
  addTrustedBlock(<?php echo json_encode($tb['title']); ?>, <?php echo json_encode($tb['points']); ?>);
  <?php endforeach; ?>
</script>
</body>
</html>
