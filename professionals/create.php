<?php
/**
 * professionals/create.php — Add New Professional
 */

$pageTitle = 'Add Professional';
$errors = [];
$success = false;
$categories = ['Makeup Artist','Hair Stylist','Nail Technician','Skincare Specialist','Wellness Expert'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $bio      = trim($_POST['bio'] ?? '');
    $status   = isset($_POST['status']) ? 1 : 0;

    if (!$name)  $errors[] = 'Full name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!$phone) $errors[] = 'Phone number is required.';
    if (!$category) $errors[] = 'Category is required.';

    if (empty($errors)) {
        // TODO: INSERT INTO professionals (...) VALUES (...)
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
        <a href="/bellavella/professionals/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Professional</h2><p class="text-sm text-gray-400 mt-0.5">Register a new beauty professional</p></div>
      </div>
      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Professional added successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside space-y-1"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
           <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div class="md:col-span-2 space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Personal Information</h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2"><label class="form-label">Full Name *</label><input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Priya Sharma" class="form-input" required></div>
                    <div><label class="form-label">Email *</label><input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="priya@example.com" class="form-input" required></div>
                    <div><label class="form-label">Phone *</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+91 98765 43210" class="form-input" required></div>
                    <div><label class="form-label">Category *</label>
                      <select name="category" class="form-input cursor-pointer">
                        <option value="">Select category</option>
                        <?php foreach($categories as $cat): ?><option value="<?php echo $cat; ?>" <?php echo (($_POST['category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option><?php endforeach; ?>
                      </select>
                    </div>
                    <div><label class="form-label">City</label><input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" placeholder="e.g. Mumbai" class="form-input"></div>
                    <div class="sm:col-span-2"><label class="form-label">Bio</label><textarea name="bio" rows="4" placeholder="Brief professional bio…" class="form-input resize-none"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea></div>
                 </div>
              </div>

              <div class="space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Profile & Settings</h3>
                 <div>
                    <label class="form-label">Profile Photo</label>
                    <div class="flex flex-col items-center gap-4">
                      <div class="relative">
                        <img id="img-preview" src="https://ui-avatars.com/api/?name=New+Pro&background=f3f4f6&color=6b7280&size=128" class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm" alt="">
                      </div>
                      <label class="w-full flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all">
                        <i data-lucide="upload" class="w-5 h-5 text-gray-300 mb-1"></i>
                        <p class="text-sm text-gray-400">Upload photo</p>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                      </label>
                    </div>
                 </div>

                 <div class="bg-gray-50 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-2">
                      <p class="text-sm font-medium text-gray-900">Active Account</p>
                      <label class="toggle-switch"><input type="checkbox" name="status" checked><span class="toggle-slider"></span></label>
                    </div>
                    <p class="text-xs text-gray-400">Allow this professional to receive bookings</p>
                 </div>
              </div>
           </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="/bellavella/professionals/" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary"><i data-lucide="user-plus" class="w-4 h-4"></i> Add Professional</button>
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
