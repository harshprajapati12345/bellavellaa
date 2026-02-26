<?php
/**
 * homepage/create.php — Add New Homepage Section
 */
$pageTitle = 'Add Section';
$errors    = [];
$success   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $content_type = trim($_POST['content_type'] ?? '');
    $title        = trim($_POST['title'] ?? '');
    $status       = isset($_POST['status']) ? 'Active' : 'Inactive';
    $action       = $_POST['form_action'] ?? 'draft';

    if (!$name)         $errors[] = 'Section name is required.';
    if (!$content_type) $errors[] = 'Content type is required.';

    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Section · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    .section-card { transition: box-shadow 0.25s ease; }
    .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
    .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.88); }
  </style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto pb-28">
    <?php include '../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- -- Page Header ------------------------------------------------ -->
      <div class="flex items-center gap-4">
        <a href="/bella/homepage/"
          class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Section</h2>
          <p class="text-sm text-gray-400 mt-0.5">Create a new homepage section</p>
        </div>
      </div>

      <?php if ($success): ?>
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
        <i data-lucide="check-circle" class="w-4 h-4"></i> Section created successfully!
      </div>
      <?php endif; ?>
      <?php if ($errors): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
        <ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="sectionForm">
      <div class="flex flex-col gap-6">

        <!-- ??? CARD 1 · SECTION DETAILS ????????????????????????????????? -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Section Details</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

              <!-- Section Name -->
              <div>
                <label class="form-label">Section Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" id="sectionName" required
                  class="form-input" placeholder="e.g. Hero Banner, About Us"
                  value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
              </div>

              <!-- Section Key (auto-generated) -->
              <div>
                <label class="form-label">Section Key</label>
                <input type="text" name="key" id="sectionKey" readonly
                  class="form-input bg-gray-50 text-gray-500 cursor-default"
                  placeholder="auto-generated"
                  value="<?php echo htmlspecialchars($_POST['key'] ?? ''); ?>">
              </div>

              <!-- Content Type -->
              <div>
                <label class="form-label">Content Type <span class="text-red-400">*</span></label>
                <select name="content_type" id="content-type" required class="form-input cursor-pointer">
                  <option value="">Select type...</option>
                  <option value="static"  <?php echo (($_POST['content_type'] ?? '') === 'static')  ? 'selected' : ''; ?>>Static Content</option>
                  <option value="dynamic" <?php echo (($_POST['content_type'] ?? '') === 'dynamic') ? 'selected' : ''; ?>>Dynamic (Linked to Data)</option>
                </select>
              </div>

              <!-- Data Source (dynamic only) -->
              <div id="dynamic-source-wrap" class="<?php echo (($_POST['content_type'] ?? '') === 'dynamic') ? '' : 'hidden'; ?>">
                <label class="form-label">Data Source</label>
                <select name="data_source" class="form-input cursor-pointer">
                  <option value="">Select source...</option>
                  <option <?php echo (($_POST['data_source'] ?? '') === 'Services')             ? 'selected' : ''; ?>>Services</option>
                  <option <?php echo (($_POST['data_source'] ?? '') === 'Packages')             ? 'selected' : ''; ?>>Packages</option>
                  <option <?php echo (($_POST['data_source'] ?? '') === 'Reviews / Testimonials') ? 'selected' : ''; ?>>Reviews / Testimonials</option>
                  <option <?php echo (($_POST['data_source'] ?? '') === 'Professionals')        ? 'selected' : ''; ?>>Professionals</option>
                  <option <?php echo (($_POST['data_source'] ?? '') === 'Gallery / Media')      ? 'selected' : ''; ?>>Gallery / Media</option>
                </select>
              </div>

              <!-- Order -->
              <div>
                <label class="form-label">Display Order</label>
                <input type="number" name="order" min="1"
                  class="form-input" placeholder="e.g. 1"
                  value="<?php echo htmlspecialchars($_POST['order'] ?? ''); ?>">
              </div>

              <!-- Status -->
              <div class="flex items-start pt-1">
                <div class="w-full py-3 px-4 bg-gray-50 rounded-xl flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Active Section</p>
                    <p class="text-xs text-gray-400">Show this section on the homepage</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" name="status" <?php echo (!isset($_POST['status']) || $_POST['status']) ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- ??? CARD 2 · SECTION CONTENT ????????????????????????????????? -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Section Content</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8 space-y-5">

            <!-- Title -->
            <div>
              <label class="form-label">Section Title</label>
              <input type="text" name="title"
                class="form-input" placeholder="e.g. Welcome to Bellavella"
                value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            </div>

            <!-- Subtitle -->
            <div>
              <label class="form-label">Subtitle <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
              <input type="text" name="subtitle"
                class="form-input" placeholder="e.g. Premium Salon &amp; Beauty Experience"
                value="<?php echo htmlspecialchars($_POST['subtitle'] ?? ''); ?>">
            </div>

            <!-- Description -->
            <div>
              <label class="form-label">Description <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
              <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <!-- Toolbar -->
                <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
                  <button type="button" onclick="execCmd('bold')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors" title="Bold">
                    <i data-lucide="bold" class="w-4 h-4"></i>
                  </button>
                  <button type="button" onclick="execCmd('italic')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors" title="Italic">
                    <i data-lucide="italic" class="w-4 h-4"></i>
                  </button>
                  <button type="button" onclick="execCmd('underline')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors" title="Underline">
                    <i data-lucide="underline" class="w-4 h-4"></i>
                  </button>
                  <div class="w-px h-5 bg-gray-200 mx-1"></div>
                  <button type="button" onclick="execCmd('insertUnorderedList')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors" title="List">
                    <i data-lucide="list" class="w-4 h-4"></i>
                  </button>
                  <button type="button" onclick="execCmd('createLink')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors" title="Link">
                    <i data-lucide="link" class="w-4 h-4"></i>
                  </button>
                </div>
                <div id="descEditor" contenteditable="true"
                  class="min-h-[160px] px-4 py-4 text-sm text-gray-700 focus:outline-none leading-relaxed"><?php echo $_POST['description'] ?? ''; ?></div>
                <input type="hidden" name="description" id="descHidden">
              </div>
            </div>

            <!-- Button Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="form-label">Button Text <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" name="btn_text"
                  class="form-input" placeholder="e.g. View All Services"
                  value="<?php echo htmlspecialchars($_POST['btn_text'] ?? ''); ?>">
              </div>
              <div>
                <label class="form-label">Button Link <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" name="btn_link"
                  class="form-input" placeholder="e.g. /services"
                  value="<?php echo htmlspecialchars($_POST['btn_link'] ?? ''); ?>">
              </div>
            </div>

          </div>
        </div>

        <!-- ??? CARD 3 · SECTION IMAGE ??????????????????????????????????? -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">3</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Section Image</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">

              <!-- Upload Zone -->
              <div>
                <label class="form-label">Background / Feature Image <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <div id="dropZone"
                  class="drop-zone relative flex flex-col items-center justify-center w-full h-52 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50/60 transition-all group"
                  onclick="document.getElementById('sectionImageInput').click()"
                  ondragover="event.preventDefault(); this.classList.add('dragover')"
                  ondragleave="this.classList.remove('dragover')"
                  ondrop="event.preventDefault(); this.classList.remove('dragover'); handleDrop(event)">
                  <div id="uploadPlaceholder" class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                      <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Click to upload or drag &amp; drop</p>
                    <p class="text-xs text-gray-400">JPG, PNG, WebP up to 4MB</p>
                  </div>
                  <input type="file" name="section_image" id="sectionImageInput" accept="image/*" class="hidden" onchange="previewImage(this)">
                </div>

                <div class="relative mt-3 hidden" id="imgPreviewWrap">
                  <img id="imgPreview" class="w-full h-44 object-cover rounded-2xl border border-gray-100" src="" alt="">
                  <button type="button" id="removeImgBtn"
                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm"
                    onclick="removeImage()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- Image Tips -->
              <div class="bg-gray-50 rounded-2xl p-5 space-y-3 text-sm text-gray-500">
                <div class="flex items-start gap-2.5">
                  <i data-lucide="info" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"></i>
                  <p><span class="font-semibold text-gray-700">Recommended size:</span> 1920 × 800px for hero banners.</p>
                </div>
                <div class="flex items-start gap-2.5">
                  <i data-lucide="image" class="w-4 h-4 text-violet-500 mt-0.5 flex-shrink-0"></i>
                  <p><span class="font-semibold text-gray-700">Format:</span> Use JPG for photos, PNG for graphics with transparency.</p>
                </div>
                <div class="flex items-start gap-2.5">
                  <i data-lucide="zap" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                  <p><span class="font-semibold text-gray-700">Performance:</span> Compress images before uploading for faster load times.</p>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- ??? STICKY ACTION BAR ????????????????????????????????????????? -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2">
          <a href="/bella/homepage/" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="form_action" value="draft" class="btn btn-secondary">
            <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
          </button>
          <button type="submit" name="form_action" value="publish" class="btn btn-primary" onclick="syncDesc()">
            <i data-lucide="globe" class="w-4 h-4"></i> Publish Section
          </button>
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

  // -- Auto-generate key from name ----------------------------------------
  document.getElementById('sectionName').addEventListener('input', function() {
    document.getElementById('sectionKey').value =
      this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  });

  // -- Show / hide data source dropdown ----------------------------------
  document.getElementById('content-type').addEventListener('change', function() {
    document.getElementById('dynamic-source-wrap').classList.toggle('hidden', this.value !== 'dynamic');
  });

  // -- Rich text editor commands ------------------------------------------
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
    document.getElementById('descHidden').value =
      document.getElementById('descEditor').innerHTML;
  }

  // Sync before any submit
  document.getElementById('sectionForm').addEventListener('submit', syncDesc);

  // -- Image upload helpers -----------------------------------------------
  function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('imgPreview').src = e.target.result;
      document.getElementById('imgPreviewWrap').classList.remove('hidden');
      document.getElementById('uploadPlaceholder').classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function removeImage() {
    document.getElementById('sectionImageInput').value = '';
    document.getElementById('imgPreviewWrap').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('imgPreview').src = '';
  }

  function handleDrop(e) {
    const dt = e.dataTransfer;
    if (dt.files && dt.files[0]) {
      const input = document.getElementById('sectionImageInput');
      // DataTransfer requires workaround for file input
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(dt.files[0]);
      input.files = dataTransfer.files;
      previewImage(input);
    }
  }
</script>
</body>
</html>
