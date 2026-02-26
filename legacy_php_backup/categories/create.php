<?php
/**
 * categories/create.php — Add New Category
 */
$pageTitle = 'Add Category';
$description = 'Create a new service category';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = isset($_POST['status']) ? 1 : 0;

    if (!$name) $errors[] = 'Category name is required.';

    if (empty($errors)) {
        // TODO: INSERT INTO categories (name, description, status) VALUES (?, ?, ?)
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
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    .form-input { width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; outline: none; transition: all 0.2s; }
    .form-input:focus { border-color: #000; ring: 2px; ring-color: rgba(0,0,0,0.05); }
    .form-label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
    .btn { padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 500; transition: all 0.2s; display: inline-flex; items-center; gap: 0.5rem; }
    .btn-primary { background: #000; color: #fff; }
    .btn-primary:hover { background: #1f2937; transform: translateY(-1px); }
    .btn-secondary { background: #fff; color: #374151; border: 1px solid #e5e7eb; }
    .btn-secondary:hover { background: #f9fafb; }
    .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e5e7eb; border-radius: 999px; transition: 0.25s; }
    .toggle-slider:before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.25s; }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(20px); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php include '../includes/header.php'; ?>
    <div class="flex flex-col gap-6">
      <div class="flex items-center gap-4">
        <a href="/bella/categories/" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight"><?php echo $pageTitle; ?></h2>
          <p class="text-sm text-gray-400 mt-0.5"><?php echo $description; ?></p>
        </div>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium shadow-sm"><i data-lucide="check-circle" class="w-5 h-5"></i> Category created successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm"><ul class="space-y-1"><?php foreach($errors as $e): ?><li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> <?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-8 lg:p-10 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-8">
              <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> General Information
              </h3>
              
              <div>
                <label class="form-label font-semibold">Category Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Bridal Glam" class="form-input" required>
              </div>
              
              <div>
                <label class="form-label font-semibold">Description</label>
                <textarea name="description" rows="5" placeholder="Tell us about this category..." class="form-input resize-none"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
              </div>
            </div>
            
            <div class="space-y-8">
              <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Visibility
              </h3>
              
              <div>
                <label class="form-label font-semibold">Cover Image</label>
                <div class="flex gap-4 items-start">
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1 pb-2">
                      <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                        <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                      </div>
                      <p class="text-sm font-medium text-gray-600">Click to upload</p>
                      <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 10MB</p>
                      <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                    <div id="preview-container" class="hidden w-40 h-40 relative group">
                        <img id="img-preview" class="w-full h-full object-cover rounded-[2rem] border border-gray-100" src="" alt="">
                        <div class="absolute inset-0 bg-black/40 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
              </div>

              <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Active Status</p>
                  <p class="text-xs text-gray-400 mt-0.5">Visible on your booking platform</p>
                </div>
                <label class="toggle-switch"><input type="checkbox" name="status" checked><span class="toggle-slider"></span></label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
          <a href="/bella/categories/" class="btn btn-secondary">Discard Changes</a>
          <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
            <i data-lucide="check" class="w-4 h-4"></i> Create Category
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('img-preview').src = e.target.result;
        document.getElementById('preview-container').classList.remove('hidden');
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
</body>
</html>
