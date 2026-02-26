<?php
/**
 * media/banners/create.php — Add New Banner
 */
$pageTitle = 'Add Banner';
$errors    = [];
$success   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $page     = trim($_POST['target_page'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $action   = $_POST['form_action'] ?? 'draft';

    if (!$title)    $errors[] = 'Banner title is required.';
    if (!$page)     $errors[] = 'Target page is required.';
    if (!$position) $errors[] = 'Position is required.';

    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Banner · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    .section-card { transition: box-shadow 0.25s ease; }
    .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.88); }
    .drop-zone { transition: border-color 0.2s, background 0.2s; }
    .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
    .drop-zone.has-file { border-color:#10b981 !important; background:#f0fdf4; }
  </style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto pb-28">
    <?php include '../../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- ── Page Header ──────────────────────────────────────────────── -->
      <div class="flex items-center gap-4">
        <a href="/bella/media/banners/"
          class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Banner</h2>
          <p class="text-sm text-gray-400 mt-0.5">Create a new promotional banner</p>
        </div>
      </div>

      <?php if ($success): ?>
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
        <i data-lucide="check-circle" class="w-4 h-4"></i> Banner created successfully!
      </div>
      <?php endif; ?>
      <?php if ($errors): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
        <ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="bannerForm">
      <div class="flex flex-col gap-6">

        <!-- ━━━ CARD 1 · BANNER DETAILS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">1</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Banner Details</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

              <!-- Banner Title -->
              <div class="sm:col-span-2">
                <label class="form-label">Banner Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" required
                  class="form-input" placeholder="e.g. Summer Collection Sale"
                  value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
              </div>

              <!-- Target Page -->
              <div>
                <label class="form-label">Target Page <span class="text-red-400">*</span></label>
                <select name="target_page" required class="form-input cursor-pointer">
                  <option value="">Select page...</option>
                  <?php foreach(['Home','Services','Packages','About Us','Contact','Offers','Professionals'] as $pg): ?>
                  <option value="<?php echo $pg; ?>" <?php echo (($_POST['target_page'] ?? '') === $pg) ? 'selected' : ''; ?>><?php echo $pg; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Position -->
              <div>
                <label class="form-label">Position <span class="text-red-400">*</span></label>
                <select name="position" required class="form-input cursor-pointer">
                  <option value="">Select position...</option>
                  <?php foreach(['Hero','Top Banner','Mid Section','Sidebar','Bottom Footer'] as $pos): ?>
                  <option value="<?php echo $pos; ?>" <?php echo (($_POST['position'] ?? '') === $pos) ? 'selected' : ''; ?>><?php echo $pos; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Display Order -->
              <div>
                <label class="form-label">Display Order</label>
                <input type="number" name="order" min="1"
                  class="form-input" placeholder="e.g. 1"
                  value="<?php echo htmlspecialchars($_POST['order'] ?? '1'); ?>">
              </div>

              <!-- Link URL -->
              <div>
                <label class="form-label">Link URL <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" name="link_url"
                  class="form-input" placeholder="e.g. /services or https://..."
                  value="<?php echo htmlspecialchars($_POST['link_url'] ?? ''); ?>">
              </div>

              <!-- Start Date -->
              <div>
                <label class="form-label">Start Date <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="date" name="start_date"
                  class="form-input"
                  value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
              </div>

              <!-- End Date -->
              <div>
                <label class="form-label">End Date <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                <input type="date" name="end_date"
                  class="form-input"
                  value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
              </div>

            </div>
          </div>
        </div>

        <!-- ━━━ CARD 2 · BANNER IMAGE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">2</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Banner Image</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

              <!-- Upload Zone -->
              <div>
                <label class="form-label">Upload Image <span class="text-red-400">*</span></label>

                <!-- Drop Zone -->
                <div id="bannerDropZone"
                  class="drop-zone relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer bg-gray-50/50 min-h-[200px] transition-all"
                  onclick="document.getElementById('bannerFileInput').click()"
                  ondragover="event.preventDefault(); this.classList.add('dragover')"
                  ondragleave="this.classList.remove('dragover')"
                  ondrop="handleDrop(event)">

                  <div id="uploadPlaceholder" class="flex flex-col items-center gap-3 py-8 px-4 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                      <i data-lucide="image-plus" class="w-7 h-7 text-blue-400"></i>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700">Click to upload or drag &amp; drop</p>
                      <p class="text-xs text-gray-400 mt-1">Recommended: 1920 × 600px · JPG, PNG, WebP</p>
                    </div>
                  </div>

                  <input type="file" name="banner_image" id="bannerFileInput"
                    accept="image/jpeg,image/png,image/webp,image/gif" class="hidden"
                    onchange="previewBanner(this)">
                </div>

                <!-- Preview -->
                <div class="relative mt-3 hidden" id="imgPreviewWrap">
                  <img id="imgPreview" class="w-full max-h-52 object-cover rounded-2xl border border-gray-100" src="" alt="">
                  <div id="fileLabel" class="text-xs text-gray-500 mt-2 text-center font-medium"></div>
                  <button type="button"
                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all shadow-sm"
                    onclick="removeBanner()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- Tips + Settings -->
              <div class="space-y-4">
                <!-- Image Tips -->
                <div class="bg-gray-50 rounded-2xl p-5 space-y-3 text-sm text-gray-500">
                  <div class="flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"></i>
                    <p><span class="font-semibold text-gray-700">Hero banners:</span> 1920×600px (16:5 ratio).</p>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <i data-lucide="image" class="w-4 h-4 text-violet-500 mt-0.5 flex-shrink-0"></i>
                    <p><span class="font-semibold text-gray-700">Format:</span> JPG for photos, PNG for graphics with transparency.</p>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <i data-lucide="zap" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <p><span class="font-semibold text-gray-700">Max size:</span> 4MB. Compress before uploading.</p>
                  </div>
                </div>

                <!-- Alt Text -->
                <div>
                  <label class="form-label">Alt Text <span class="text-xs text-gray-400 font-normal">(SEO)</span></label>
                  <input type="text" name="alt_text"
                    class="form-input" placeholder="Describe the banner image for accessibility"
                    value="<?php echo htmlspecialchars($_POST['alt_text'] ?? ''); ?>">
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- ━━━ CARD 3 · DISPLAY SETTINGS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden section-card">
          <div class="px-8 pt-7 pb-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold">3</div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest">Display Settings</h3>
              <div class="flex-1 h-px bg-gray-100 ml-2"></div>
            </div>
          </div>
          <div class="px-8 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

              <!-- Full Width -->
              <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-2xl">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Full Width</p>
                  <p class="text-[11px] text-gray-400 mt-0.5">Span entire page width</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" name="is_full_width" <?php echo isset($_POST['is_full_width']) ? 'checked' : ''; ?>>
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Open in New Tab -->
              <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-2xl">
                <div>
                  <p class="text-sm font-semibold text-gray-900">New Tab</p>
                  <p class="text-[11px] text-gray-400 mt-0.5">Open link in new tab</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" name="open_new_tab" <?php echo isset($_POST['open_new_tab']) ? 'checked' : ''; ?>>
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Active Status -->
              <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-2xl">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Active</p>
                  <p class="text-[11px] text-gray-400 mt-0.5">Visible to users</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" name="status" <?php echo (!isset($_POST['status']) || $_POST['status']) ? 'checked' : ''; ?>>
                  <span class="toggle-slider"></span>
                </label>
              </div>

            </div>
          </div>
        </div>

        <!-- ━━━ STICKY ACTION BAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2">
          <a href="/bella/media/banners/" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="form_action" value="draft" class="btn btn-secondary">
            <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
          </button>
          <button type="submit" name="form_action" value="publish" class="btn btn-primary">
            <i data-lucide="upload" class="w-4 h-4"></i> Publish Banner
          </button>
        </div>

      </div>
      </form>

    </div>
  </main>
</div>
<?php include '../../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  // ── Image preview ──────────────────────────────────────────────────────
  function previewBanner(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (!file.type.startsWith('image/')) {
      Swal.fire({ title: 'Invalid File', text: 'Only image files are allowed.', icon: 'error', confirmButtonColor: '#000' });
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('imgPreview').src = e.target.result;
      document.getElementById('fileLabel').textContent =
        file.name + ' (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)';
      document.getElementById('imgPreviewWrap').classList.remove('hidden');
      document.getElementById('uploadPlaceholder').classList.add('hidden');
      document.getElementById('bannerDropZone').classList.add('has-file');
    };
    reader.readAsDataURL(file);
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function removeBanner() {
    document.getElementById('bannerFileInput').value = '';
    document.getElementById('imgPreviewWrap').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('bannerDropZone').classList.remove('has-file');
    document.getElementById('imgPreview').src = '';
  }

  function handleDrop(e) {
    e.preventDefault();
    document.getElementById('bannerDropZone').classList.remove('dragover');
    const input = document.getElementById('bannerFileInput');
    const dt = new DataTransfer();
    if (e.dataTransfer.files[0]) {
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      previewBanner(input);
    }
  }
</script>
</body>
</html>
