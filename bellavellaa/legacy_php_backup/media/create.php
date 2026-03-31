<?php
/**
 * media/create.php — Upload Media (Image or Video)
 */
$pageTitle = 'Upload Media';
$errors    = [];
$success   = false;

$typeParam    = $_GET['type']    ?? '';
$sectionParam = $_GET['section'] ?? '';
$pageParam    = $_GET['page']    ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $media_type = trim($_POST['media_type'] ?? '');
    $title      = trim($_POST['title'] ?? '');
    $section    = trim($_POST['linked_section'] ?? '');
    $page       = trim($_POST['target_page'] ?? '');

    if (!$media_type) $errors[] = 'Media type is required.';
    if (!$title)      $errors[] = 'Title is required.';
    if (!$section)    $errors[] = 'Linked section is required.';
    if (!$page)       $errors[] = 'Target page is required.';

    if (empty($errors)) $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Media · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    .section-card { transition: box-shadow 0.25s ease; }
    .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .sticky-bar { position:sticky; bottom:0; z-index:30; backdrop-filter:blur(12px); background:rgba(255,255,255,0.88); }
    .drop-zone { transition: border-color 0.2s, background 0.2s; }
    .drop-zone.dragover { border-color:#000 !important; background:#fafafa; }
    .drop-zone.has-file { border-color:#10b981 !important; background:#f0fdf4; }
    .locked-select { background-color:#f9fafb !important; cursor:not-allowed; pointer-events:none; color:#6b7280; }
  </style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include __DIR__ . '/../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto pb-28">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- ── Page Header ──────────────────────────────────────────────── -->
      <div class="flex items-center gap-4">
        <a href="/bella/media/"
          class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Upload Media</h2>
          <p class="text-sm text-gray-400 mt-0.5">Add a new image or video to the media library</p>
        </div>
      </div>

      <?php if ($success): ?>
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
        <i data-lucide="check-circle" class="w-4 h-4"></i> Media uploaded successfully!
      </div>
      <?php endif; ?>
      <?php if ($errors): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
        <ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="mediaForm">
      <div class="flex flex-col gap-6">

        <!-- ━━━ CARD 1 · MEDIA DETAILS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
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
                <label class="form-label">Media Type <span class="text-red-400">*</span></label>
                <select name="media_type" id="media-type" required
                  class="form-input cursor-pointer <?php echo !empty($typeParam) ? 'locked-select' : ''; ?>">
                  <option value="">Select type...</option>
                  <option value="image" <?php echo $typeParam === 'image' ? 'selected' : ''; ?>>Image</option>
                  <option value="video" <?php echo $typeParam === 'video' ? 'selected' : ''; ?>>Video</option>
                </select>
              </div>

              <!-- Title -->
              <div>
                <label class="form-label">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" required
                  class="form-input" placeholder="e.g. Hero Slide 1, Promo Video"
                  value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
              </div>

              <!-- Linked Section -->
              <div>
                <label class="form-label">Linked Section <span class="text-red-400">*</span></label>
                <select name="linked_section" required class="form-input cursor-pointer">
                  <option value="">Select section...</option>
                  <?php foreach(['Top Banner','Mid Section','Side Gallery','Bottom Footer','Hero Slider','Services'] as $s): ?>
                  <option value="<?php echo $s; ?>" <?php echo ($sectionParam === $s || ($_POST['linked_section'] ?? '') === $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Target Page -->
              <div>
                <label class="form-label">Target Page <span class="text-red-400">*</span></label>
                <select name="target_page" required class="form-input cursor-pointer">
                  <option value="">Select page...</option>
                  <?php foreach(['Home','Services','Packages','About Us','Contact','Professionals','Offers'] as $pg): ?>
                  <option value="<?php echo $pg; ?>" <?php echo ($pageParam === $pg || ($_POST['target_page'] ?? '') === $pg) ? 'selected' : ''; ?>><?php echo $pg; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Display Order -->
              <div>
                <label class="form-label">Display Order</label>
                <input type="number" name="order" min="0"
                  class="form-input" placeholder="e.g. 1"
                  value="<?php echo htmlspecialchars($_POST['order'] ?? '1'); ?>">
              </div>

              <!-- Status -->
              <div class="flex items-start pt-1">
                <div class="w-full py-3 px-4 bg-gray-50 rounded-xl flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Active</p>
                    <p class="text-xs text-gray-400">Visible to users</p>
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

        <!-- ━━━ CARD 2 · FILE UPLOAD ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
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
                <label class="form-label">Media File <span class="text-red-400">*</span></label>

                <!-- Type hint: image -->
                <div id="hint-image" class="<?php echo ($typeParam === 'image') ? '' : 'hidden'; ?> mb-3 flex items-center gap-2 px-4 py-2.5 bg-blue-50 rounded-xl">
                  <i data-lucide="image" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                  <p class="text-xs text-blue-700 font-medium">Upload high-quality landscape images (1920×1080) for best results.</p>
                </div>
                <!-- Type hint: video -->
                <div id="hint-video" class="<?php echo ($typeParam === 'video') ? '' : 'hidden'; ?> mb-3 flex items-center gap-2 px-4 py-2.5 bg-violet-50 rounded-xl">
                  <i data-lucide="video" class="w-4 h-4 text-violet-500 flex-shrink-0"></i>
                  <p class="text-xs text-violet-700 font-medium">Portrait/Reel videos (9:16) work best for mobile. MP4 recommended.</p>
                </div>

                <div id="mainDropZone"
                  class="drop-zone relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer bg-gray-50/50 min-h-[200px] transition-all"
                  onclick="document.getElementById('file-input').click()"
                  ondragover="event.preventDefault(); this.classList.add('dragover')"
                  ondragleave="this.classList.remove('dragover')"
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
                    accept="<?php echo ($typeParam === 'image') ? 'image/*' : (($typeParam === 'video') ? 'video/*' : 'image/*,video/*'); ?>"
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
                <div id="thumbnail-wrap" class="<?php echo ($typeParam === 'video') ? '' : 'hidden'; ?>">
                  <label class="form-label">Video Thumbnail <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                  <div id="thumbDropZone"
                    class="drop-zone relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer bg-gray-50/50 min-h-[140px] transition-all"
                    onclick="document.getElementById('thumb-input').click()"
                    ondragover="event.preventDefault(); this.classList.add('dragover')"
                    ondragleave="this.classList.remove('dragover')"
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

        <!-- ━━━ STICKY ACTION BAR ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
        <div class="sticky-bar rounded-2xl border border-gray-100 shadow-lg px-8 py-4 flex items-center justify-end gap-3 mt-2">
          <a href="/bella/media/" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="form_action" value="draft" class="btn btn-secondary">
            <i data-lucide="file-text" class="w-4 h-4"></i> Save as Draft
          </button>
          <button type="submit" name="form_action" value="upload" class="btn btn-primary">
            <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload Media
          </button>
        </div>

      </div>
      </form>

    </div>
  </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  // ── Toggle thumbnail & hints when type changes ─────────────────────────
  document.getElementById('media-type').addEventListener('change', function() {
    const isVideo = this.value === 'video';
    const isImage = this.value === 'image';

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

  // ── Main file preview ──────────────────────────────────────────────────
  function previewUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('imgPreviewWrap').classList.remove('hidden');
        document.getElementById('main-placeholder').classList.add('hidden');
        document.getElementById('mainDropZone').classList.add('has-file');
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
      document.getElementById('mainDropZone').classList.add('has-file');
      lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
    }
  }

  function removeMainFile() {
    document.getElementById('file-input').value = '';
    document.getElementById('imgPreviewWrap').classList.add('hidden');
    document.getElementById('main-placeholder').classList.remove('hidden');
    document.getElementById('mainDropZone').classList.remove('has-file');
    document.getElementById('preview-img').src = '';
  }

  function handleMainDrop(e) {
    e.preventDefault();
    document.getElementById('mainDropZone').classList.remove('dragover');
    const input = document.getElementById('file-input');
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      previewUpload(input);
    }
  }

  // ── Thumbnail preview ──────────────────────────────────────────────────
  function previewThumb(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('thumb-img').src = e.target.result;
      document.getElementById('thumbPreviewWrap').classList.remove('hidden');
      document.getElementById('thumb-placeholder').classList.add('hidden');
      document.getElementById('thumbDropZone').classList.add('has-file');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function removeThumb() {
    document.getElementById('thumb-input').value = '';
    document.getElementById('thumbPreviewWrap').classList.add('hidden');
    document.getElementById('thumb-placeholder').classList.remove('hidden');
    document.getElementById('thumbDropZone').classList.remove('has-file');
    document.getElementById('thumb-img').src = '';
  }

  function handleThumbDrop(e) {
    e.preventDefault();
    document.getElementById('thumbDropZone').classList.remove('dragover');
    const input = document.getElementById('thumb-input');
    if (e.dataTransfer.files[0]) {
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      previewThumb(input);
    }
  }
</script>
</body>
</html>
