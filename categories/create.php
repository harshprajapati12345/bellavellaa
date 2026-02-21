<?php
/**
 * categories/create.php — Add New Category
 */
$type = $_GET['type'] ?? '';
$isParent = $type === 'parent';
$pageTitle = $isParent ? 'Add Parent Category' : 'Add Category';
$description = $isParent ? 'Create a new top-level service category' : 'Create a new subcategory';

$success = false;
$errors = [];

$parents = [
  ['id'=>1,'name'=>'Luxe'],
  ['id'=>2,'name'=>'Prime'],
];

$preParentId = intval($_GET['parent'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $parent_id   = intval($_POST['parent_id'] ?? 0);
    $status      = isset($_POST['status']) ? 1 : 0;

    if (!$name) $errors[] = 'Category name is required.';

    if (empty($errors)) {
        // TODO: INSERT INTO categories (name, description, parent_id, status) VALUES (?, ?, ?, ?)
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
        <a href="/bellavella/categories/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight"><?php echo $pageTitle; ?></h2>
          <p class="text-sm text-gray-400 mt-0.5"><?php echo $description; ?></p>
        </div>
      </div>
      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Category created!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="p-8 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">General Information</h3>
              
              <div>
                <label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-input cursor-pointer">
                  <option value="0" <?php echo ($isParent) ? 'selected' : ''; ?>>— Top-level Category —</option>
                  <?php foreach($parents as $p): ?>
                  <option value="<?php echo $p['id']; ?>" <?php echo ((($_POST['parent_id'] ?? $preParentId) == $p['id']) && !$isParent) ? 'selected' : ''; ?>>
                    <?php echo $p['name']; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-400 mt-1.5 ml-1">Select a parent to create a subcategory.</p>
              </div>

              <div>
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Bridal" class="form-input" required>
              </div>
              <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="5" placeholder="Brief description of this category…" class="form-input resize-none"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
              </div>
            </div>
            
            <div class="space-y-6">
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Media & Settings</h3>
              <div>
                <label class="form-label">Icon / Image</label>
                <div class="flex gap-4 items-start">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all flex-1">
                      <i data-lucide="image-plus" class="w-7 h-7 text-gray-300 mb-2"></i>
                      <p class="text-sm text-gray-400">Upload icon or image</p>
                      <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                    <img id="img-preview" class="hidden w-32 h-32 object-cover rounded-2xl border border-gray-100" src="" alt="">
                </div>
              </div>
              <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-2xl">
                <div><p class="text-sm font-medium text-gray-900">Active</p><p class="text-xs text-gray-400">Show this category to customers</p></div>
                <label class="toggle-switch"><input type="checkbox" name="status" checked><span class="toggle-slider"></span></label>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="/bellavella/categories/" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Save Category</button>
        </div>
      </form>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });</script>
</body>
</html>
