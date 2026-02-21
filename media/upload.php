<?php
/**
 * media/upload.php — Upload Media File (Banner or Video)
 */
$pageTitle = 'Upload Media';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type  = trim($_POST['type'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if (!$title) $errors[] = 'Title is required.';
    if (!$type)  $errors[] = 'Media type is required.';
    if (empty($_FILES['file']['name'])) $errors[] = 'Please select a file to upload.';

    if (empty($errors)) {
        // TODO: Handle file upload + INSERT INTO media (type, title, path) VALUES (...)
        $success = true;
    }
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
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php include '../includes/header.php'; ?>
    <div class="flex flex-col gap-6">
      <div class="flex items-center gap-4">
        <a href="/bellavella/media/<?php echo (($_GET['type'] ?? '') === 'video') ? 'videos' : 'banners'; ?>/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Upload Media</h2>
          <p class="text-sm text-gray-400 mt-0.5">Add a new banner or video to the library</p>
        </div>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> File uploaded successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside space-y-1"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left: Type & Details -->
            <div class="space-y-6">
              <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Media Settings</h3>
              
              <div>
                <label class="form-label">Media Type *</label>
                <div class="grid grid-cols-2 gap-4">
                  <label class="flex items-center gap-3 p-4 border rounded-2xl cursor-pointer transition-all <?php echo (($_POST['type'] ?? $_GET['type'] ?? '') === 'banner' || (!isset($_GET['type']) && !isset($_POST['type']))) ? 'border-black bg-gray-50 ring-1 ring-black/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'; ?>">
                    <input type="radio" name="type" value="banner" class="accent-black" <?php echo (($_POST['type'] ?? $_GET['type'] ?? '') === 'banner' || (!isset($_GET['type']) && !isset($_POST['type']))) ? 'checked' : ''; ?>>
                    <div>
                      <div class="flex items-center gap-2 mb-0.5">
                        <i data-lucide="image" class="w-4 h-4 text-gray-600"></i>
                        <span class="text-sm font-medium text-gray-900">Banner</span>
                      </div>
                      <p class="text-xs text-gray-400">Static image (JPG/PNG)</p>
                    </div>
                  </label>
                  
                  <label class="flex items-center gap-3 p-4 border rounded-2xl cursor-pointer transition-all <?php echo (($_POST['type'] ?? $_GET['type'] ?? '') === 'video') ? 'border-black bg-gray-50 ring-1 ring-black/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'; ?>">
                    <input type="radio" name="type" value="video" class="accent-black" <?php echo (($_POST['type'] ?? $_GET['type'] ?? '') === 'video') ? 'checked' : ''; ?>>
                    <div>
                      <div class="flex items-center gap-2 mb-0.5">
                        <i data-lucide="video" class="w-4 h-4 text-gray-600"></i>
                        <span class="text-sm font-medium text-gray-900">Video</span>
                      </div>
                      <p class="text-xs text-gray-400">MP4, MOV, WebM</p>
                    </div>
                  </label>
                </div>
              </div>

              <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" placeholder="e.g. Summer Sale Promotional" class="form-input" required>
                <p class="text-xs text-gray-400 mt-2">Descriptive title for internal reference.</p>
              </div>
            </div>

            <!-- Right: Upload Area -->
            <div class="space-y-6">
              <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">File Upload</h3>
              
              <div>
                <label class="form-label">Select File *</label>
                <label id="drop-zone" class="flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all bg-gray-50/50">
                  <div id="upload-placeholder" class="text-center">
                    <div class="w-12 h-12 bg-white rounded-full shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-3">
                      <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Click to upload or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">High resolution recommended (Max 50MB)</p>
                  </div>
                  <input type="file" name="file" id="file-input" class="hidden" onchange="handleFileSelect(this)">
                </label>

                <!-- File Info Preview -->
                <div id="file-info" class="hidden mt-4 bg-gray-900 text-white p-4 rounded-xl flex items-center justify-between shadow-lg shadow-gray-200">
                  <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                      <i data-lucide="file" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="min-w-0">
                      <p id="file-name" class="text-sm font-medium truncate">filename.jpg</p>
                      <p id="file-size" class="text-xs text-gray-400">0.00 MB</p>
                    </div>
                  </div>
                  <button type="button" onclick="clearFile()" class="p-2 hover:bg-white/10 rounded-lg transition-colors text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="/bellavella/media/<?php echo (($_GET['type'] ?? '') === 'video') ? 'videos' : 'banners'; ?>/" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary min-w-[120px]"><i data-lucide="upload" class="w-4 h-4"></i> Upload Media</button>
        </div>
      </form>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>
lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
function handleFileSelect(input) {
  if (!input.files || !input.files[0]) return;
  const f = input.files[0];
  document.getElementById('file-name').textContent = f.name;
  document.getElementById('file-size').textContent = (f.size / 1024 / 1024).toFixed(2) + ' MB';
  document.getElementById('file-info').classList.remove('hidden'); 
  document.getElementById('upload-placeholder').classList.add('opacity-50');
}
function clearFile() {
  document.getElementById('file-input').value = '';
  document.getElementById('file-info').classList.add('hidden');
  document.getElementById('upload-placeholder').classList.remove('opacity-50');
}
// Drag & drop
const dz = document.getElementById('drop-zone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-black','bg-gray-100'); });
dz.addEventListener('dragleave', () => dz.classList.remove('border-black','bg-gray-100'));
dz.addEventListener('drop', e => {
  e.preventDefault(); dz.classList.remove('border-black','bg-gray-100');
  const fi = document.getElementById('file-input');
  fi.files = e.dataTransfer.files;
  handleFileSelect(fi);
});
</script>
</body>
</html>
