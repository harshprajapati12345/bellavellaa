<?php
/**
 * users/edit.php — Edit User
 */

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/users/'); exit; }

// Mock Data
$user = ['id'=>$id,'name'=>'Ananya Kapoor','email'=>'ananya@example.com','phone'=>'+91 99887 76655','city'=>'Delhi','zip'=>'110001','address'=>'Block C, Vasant Vihar','status'=>'Active','avatar'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'];

$pageTitle = 'Edit User';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save logic...
    $success = true;
    $user['name'] = $_POST['name']; // Update mock for display
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit <?php echo htmlspecialchars($user['name']); ?> · Bellavella Admin</title>
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
        <a href="/bella/users/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit User</h2>
          <p class="text-sm text-gray-400 mt-0.5">#<?php echo $id; ?></p>
        </div>
        <a href="delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this user?')" class="text-red-500 hover:bg-white hover:shadow-sm px-4 py-2 rounded-xl text-sm font-medium transition-all">Delete User</a>
      </div>

      <?php if ($success): ?><div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Changes saved successfully!</div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left: Info -->
            <div class="md:col-span-2 space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Personal Details</h3>
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div class="sm:col-span-2">
                     <label class="form-label">Full Name *</label>
                     <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-input" required>
                  </div>
                  <div>
                     <label class="form-label">Email Address *</label>
                     <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-input" required>
                  </div>
                  <div>
                     <label class="form-label">Phone Number</label>
                     <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-input">
                  </div>
                  <div>
                     <label class="form-label">City</label>
                     <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" class="form-input">
                  </div>
                  <div>
                     <label class="form-label">Postal Code</label>
                     <input type="text" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>" class="form-input">
                  </div>
                  <div class="sm:col-span-2">
                     <label class="form-label">Address</label>
                     <textarea name="address" rows="3" class="form-input resize-none"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                  </div>
               </div>
            </div>

            <!-- Right: Media & Settings -->
            <div class="space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Profile & Status</h3>
               
               <div>
                  <label class="form-label">Profile Photo</label>
                  <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all relative group overflow-hidden">
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-40 transition-all" alt="">
                    <div class="absolute inset-0 flex flex-col items-center justify-center z-10">
                        <i data-lucide="camera" class="w-6 h-6 text-gray-800 mb-2"></i>
                        <p class="text-xs text-gray-800 font-medium bg-white/80 px-2 py-1 rounded-md">Change photo</p>
                    </div>
                    <input type="file" name="avatar" class="hidden">
                  </label>
               </div>

               <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                 <div><p class="text-sm font-medium text-gray-900">Active Account</p><p class="text-xs text-gray-400">User can log in</p></div>
                 <label class="toggle-switch"><input type="checkbox" name="status" <?php echo $user['status'] === 'Active' ? 'checked' : ''; ?>><span class="toggle-slider"></span></label>
               </div>

               <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                  <p class="text-sm text-blue-800 font-medium">Reset Password</p>
                  <p class="text-xs text-blue-600 mt-1 mb-3">Send a password reset email to the user.</p>
                  <button type="button" class="text-xs bg-white border border-blue-200 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-all">Send Email</button>
               </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="/bella/users/" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
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
