<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/services/'); exit; }
$service = ['id'=>$id,'name'=>'Bridal Makeup','category'=>'Makeup','price'=>2999,'duration'=>'90 mins','description'=>'Full bridal makeup with airbrush finish.','status'=>1,'image'=>''];
$pageTitle = 'Edit Service';
$errors = [];
$success = false;
$categories = ['Bridal','Hair','Makeup','Nails','Skincare','Wellness'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service['name']        = trim($_POST['name'] ?? '');
    $service['category']    = trim($_POST['category'] ?? '');
    $service['price']       = floatval($_POST['price'] ?? 0);
    $service['duration']    = trim($_POST['duration'] ?? '');
    $service['description'] = trim($_POST['description'] ?? '');
    $service['status']      = isset($_POST['status']) ? 1 : 0;
    if (!$service['name'])     $errors[] = 'Name required.';
    if (!$service['category']) $errors[] = 'Category required.';
    if (empty($errors)) { $success = true; }
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
        <a href="/bellavella/services/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Service</h2>
          <p class="text-sm text-gray-400 mt-0.5"><?php echo htmlspecialchars($service['name']); ?></p>
        </div>
      </div>
      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Service updated!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
           <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Basic Details</h3>
                 <div>
                    <label class="form-label">Service Name *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" class="form-input" required>
                 </div>
                 <div>
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-input cursor-pointer">
                      <option value="">Select category</option>
                      <?php foreach($categories as $cat): ?>
                      <option value="<?php echo $cat; ?>" <?php echo ($service['category'] === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                      <?php endforeach; ?>
                    </select>
                 </div>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                   <div>
                       <label class="form-label">Price (₹) *</label>
                       <input type="number" name="price" value="<?php echo $service['price']; ?>" min="0" step="0.01" class="form-input">
                   </div>
                   <div>
                       <label class="form-label">Duration</label>
                       <input type="text" name="duration" value="<?php echo htmlspecialchars($service['duration']); ?>" class="form-input">
                   </div>
                 </div>
                 <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-input resize-none"><?php echo htmlspecialchars($service['description']); ?></textarea>
                 </div>
              </div>

              <div class="space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Media & Visibility</h3>
                 <div>
                    <label class="form-label">Service Image</label>
                    <div class="flex gap-4 items-start">
                       <label class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all flex-1">
                          <i data-lucide="image-plus" class="w-7 h-7 text-gray-300 mb-2"></i>
                          <p class="text-sm text-gray-400">Replace image</p>
                          <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                       </label>
                       <?php if ($service['image']): ?>
                       <img src="<?php echo htmlspecialchars($service['image']); ?>" class="w-44 h-44 object-cover rounded-2xl border border-gray-100 shrink-0" alt="">
                       <?php endif; ?>
                    </div>
                    <img id="img-preview" class="hidden mt-4 w-full h-48 object-cover rounded-2xl border border-gray-100" src="" alt="">
                 </div>
                 <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-2xl">
                   <div><p class="text-sm font-medium text-gray-900">Active</p><p class="text-xs text-gray-400">Show this service to customers</p></div>
                   <label class="toggle-switch"><input type="checkbox" name="status" <?php echo $service['status'] ? 'checked' : ''; ?>><span class="toggle-slider"></span></label>
                 </div>
              </div>
           </div>
        </div>
        <div class="flex items-center justify-between px-8 py-5 bg-gray-50/50">
          <a href="/bellavella/services/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this service?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete</a>
          <div class="flex gap-3">
            <a href="/bellavella/services/" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
          </div>
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
