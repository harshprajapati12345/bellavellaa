<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/professionals/'); exit; }
$pro = ['id'=>$id,'name'=>'Priya Sharma','email'=>'priya@example.com','phone'=>'+91 98765 43210','category'=>'Makeup Artist','city'=>'Mumbai','bio'=>'Expert bridal makeup artist with 8 years of experience.','status'=>1,'verified'=>1,'photo'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'];
$pageTitle = 'Edit Professional';
$errors = [];
$success = false;
$categories = ['Makeup Artist','Hair Stylist','Nail Technician','Skincare Specialist','Wellness Expert'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pro['name']     = trim($_POST['name'] ?? '');
    $pro['email']    = trim($_POST['email'] ?? '');
    $pro['phone']    = trim($_POST['phone'] ?? '');
    $pro['category'] = trim($_POST['category'] ?? '');
    $pro['city']     = trim($_POST['city'] ?? '');
    $pro['bio']      = trim($_POST['bio'] ?? '');
    $pro['status']   = isset($_POST['status']) ? 1 : 0;
    if (!$pro['name'])  $errors[] = 'Name required.';
    if (!$pro['email']) $errors[] = 'Email required.';
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
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php include '../includes/header.php'; ?>
    <div class="flex flex-col gap-6">
      <div class="flex items-center gap-4">
        <a href="/bella/professionals/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1"><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Professional</h2><p class="text-sm text-gray-400 mt-0.5"><?php echo htmlspecialchars($pro['name']); ?></p></div>
        <a href="/bella/professionals/view.php?id=<?php echo $id; ?>" class="btn btn-secondary text-sm"><i data-lucide="eye" class="w-4 h-4"></i> View</a>
      </div>
      <?php if ($success): ?><div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Updated!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
           <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div class="md:col-span-2 space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Personal Information</h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2"><label class="form-label">Full Name *</label><input type="text" name="name" value="<?php echo htmlspecialchars($pro['name']); ?>" class="form-input" required></div>
                    <div><label class="form-label">Email *</label><input type="email" name="email" value="<?php echo htmlspecialchars($pro['email']); ?>" class="form-input" required></div>
                    <div><label class="form-label">Phone</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($pro['phone']); ?>" class="form-input"></div>
                    <div><label class="form-label">Category</label><select name="category" class="form-input cursor-pointer"><?php foreach($categories as $cat): ?><option value="<?php echo $cat; ?>" <?php echo $pro['category']===$cat?'selected':''; ?>><?php echo $cat; ?></option><?php endforeach; ?></select></div>
                    <div><label class="form-label">City</label><input type="text" name="city" value="<?php echo htmlspecialchars($pro['city']); ?>" class="form-input"></div>
                    <div class="sm:col-span-2"><label class="form-label">Bio</label><textarea name="bio" rows="4" class="form-input resize-none"><?php echo htmlspecialchars($pro['bio']); ?></textarea></div>
                 </div>
              </div>

              <div class="space-y-6">
                 <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Profile & Settings</h3>
                 <div>
                    <label class="form-label">Profile Photo</label>
                    <div class="flex flex-col items-center gap-4">
                      <div class="relative">
                        <img id="img-preview" src="<?php echo htmlspecialchars($pro['photo']); ?>" class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm" alt="">
                      </div>
                      <label class="w-full flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all">
                        <i data-lucide="upload" class="w-5 h-5 text-gray-300 mb-1"></i>
                        <p class="text-sm text-gray-400">Replace photo</p>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                      </label>
                    </div>
                 </div>

                 <div class="bg-gray-50 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-2">
                       <p class="text-sm font-medium text-gray-900">Active Account</p>
                       <label class="toggle-switch"><input type="checkbox" name="status" <?php echo $pro['status']?'checked':''; ?>><span class="toggle-slider"></span></label>
                    </div>
                    <p class="text-xs text-gray-400">Allow bookings</p>
                 </div>
              </div>
           </div>
        </div>

        <div class="flex items-center justify-between px-8 py-5 bg-gray-50/50">
          <a href="/bella/professionals/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this professional?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete</a>
          <div class="flex gap-3"><a href="/bella/professionals/" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Update</button></div>
        </div>
      </form>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });</script>
</body>
</html>
