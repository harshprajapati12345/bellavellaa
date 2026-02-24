<?php
/**
 * categories/edit.php — Edit Category
 */
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/categories/'); exit; }

// Mock data for individual category
$category = [
    'id' => $id,
    'name' => 'Bridal Glam',
    'description' => 'Comprehensive bridal beauty packages including hair, makeup, and skin rituals designed for the modern bride.',
    'status' => 1,
    'image' => 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=800&q=80',
    'slug' => 'bridal-glam'
];

$pageTitle = 'Edit Category';
$description = 'Update category details and media';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category['name']        = trim($_POST['name'] ?? '');
    $category['description'] = trim($_POST['description'] ?? '');
    $category['status']      = isset($_POST['status']) ? 1 : 0;

    if (!$category['name']) $errors[] = 'Category name is required.';

    if (empty($errors)) {
        // TODO: UPDATE categories SET name=?, description=?, status=? WHERE id=?
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
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    .form-input { width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; outline: none; transition: all 0.2s; background: white; }
    .form-input:focus { border-color: #000; ring: 2px; ring-color: rgba(0,0,0,0.05); }
    .form-label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
    .btn { padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 500; transition: all 0.2s; display: inline-flex; items-center; gap: 0.5rem; }
    .btn-primary { background: #000; color: #fff; }
    .btn-primary:hover { background: #1f2937; transform: translateY(-1px); }
    .btn-secondary { background: #fff; color: #374151; border: 1px solid #e5e7eb; }
    .btn-secondary:hover { background: #f9fafb; }
    .btn-danger { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
    .btn-danger:hover { background: #fee2e2; }
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
        <a href="/bellavella/categories/" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Category</h2>
          <p class="text-sm text-gray-400 mt-0.5">Editing: <span class="text-black font-medium"><?php echo htmlspecialchars($category['name']); ?></span></p>
        </div>
      </div>

      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium shadow-sm animate-in fade-in slide-in-from-top-2 duration-300"><i data-lucide="check-circle" class="w-5 h-5"></i> Category updated successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm"><ul class="space-y-1"><?php foreach($errors as $e): ?><li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> <?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-8 lg:p-10 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Basic Details
                    </h3>
                    
                    <div>
                      <label class="form-label font-semibold">Category Name *</label>
                      <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" class="form-input" required>
                    </div>
                    
                    <div>
                      <label class="form-label font-semibold">Description</label>
                      <textarea name="description" rows="6" class="form-input resize-none"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                </div>

                <div class="space-y-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Controls
                    </h3>

                    <div>
                      <label class="form-label font-semibold">Icon / Image</label>
                      <div class="flex gap-5 items-start">
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2.2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1 pb-2">
                            <div class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <i data-lucide="image-plus" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600">Change image</p>
                            <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </label>
                        <div class="w-40 h-40 relative group overflow-hidden rounded-[2.2rem]">
                            <img id="img-preview" src="<?php echo htmlspecialchars($category['image']); ?>" class="w-full h-full object-cover border border-gray-100" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-default">
                                <span class="text-[10px] text-white font-bold uppercase tracking-wider">Current Image</span>
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="flex items-center justify-between p-7 bg-[#F9F9F9] rounded-[2rem] border border-gray-50">
                      <div>
                        <p class="text-sm font-bold text-gray-900">Active Visibility</p>
                        <p class="text-xs text-gray-400 mt-1">Make this visible on frontend</p>
                      </div>
                      <label class="toggle-switch"><input type="checkbox" name="status" <?php echo $category['status'] ? 'checked' : ''; ?>><span class="toggle-slider"></span></label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-between px-10 py-6 bg-[#F9F9F9]/50">
          <a href="/bellavella/categories/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this category?')" class="btn btn-danger text-sm px-6">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Category
          </a>
          <div class="flex gap-3">
            <a href="/bellavella/categories/" class="btn btn-secondary px-8">Cancel</a>
            <button type="submit" class="btn btn-primary lg:px-12 shadow-lg shadow-black/10">
              <i data-lucide="save" class="w-4 h-4"></i> Save Updates
            </button>
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
  
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('img-preview').src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
</body>
</html>
