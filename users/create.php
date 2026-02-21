<?php
/**
 * users/create.php — Add New User
 */

$pageTitle = 'Add User';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $status   = isset($_POST['status']) ? 'Active' : 'Inactive';

    if (!$name)  $errors[] = 'Name is required.';
    if (!$email) $errors[] = 'Email is required.';
    
    if (empty($errors)) {
        // TODO: INSERT INTO users ...
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User · Bellavella Admin</title>
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
        <a href="/bellavella/users/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add User</h2>
          <p class="text-sm text-gray-400 mt-0.5">Create a new customer account</p>
        </div>
      </div>

      <?php if ($success): ?><div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> User created successfully!</div><?php endif; ?>
      <?php if ($errors): ?><div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl"><ul class="list-disc list-inside text-sm"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left: Info -->
            <div class="md:col-span-2 space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Personal Details</h3>
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div class="sm:col-span-2">
                     <label class="form-label">Full Name *</label>
                     <input type="text" name="name" class="form-input" placeholder="e.g. Ananya Kapoor" required>
                  </div>
                  <div>
                     <label class="form-label">Email Address *</label>
                     <input type="email" name="email" class="form-input" placeholder="e.g. ananya@example.com" required>
                  </div>
                  <div>
                     <label class="form-label">Phone Number</label>
                     <input type="tel" name="phone" class="form-input" placeholder="+91 00000 00000">
                  </div>
                  <div>
                     <label class="form-label">City</label>
                     <input type="text" name="city" class="form-input" placeholder="e.g. Delhi">
                  </div>
                  <div>
                     <label class="form-label">Postal Code</label>
                     <input type="text" name="zip" class="form-input" placeholder="e.g. 110001">
                  </div>
                  <div class="sm:col-span-2">
                     <label class="form-label">Address</label>
                     <textarea name="address" rows="3" class="form-input resize-none" placeholder="Full address..."></textarea>
                  </div>
               </div>
            </div>

            <!-- Right: Media & Settings -->
            <div class="space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Profile & Status</h3>
               
               <div>
                  <label class="form-label">Profile Photo</label>
                  <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all">
                    <i data-lucide="camera" class="w-6 h-6 text-gray-300 mb-2"></i>
                    <p class="text-xs text-gray-400">Upload photo</p>
                    <input type="file" name="avatar" class="hidden">
                  </label>
               </div>

               <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                 <div><p class="text-sm font-medium text-gray-900">Active Account</p><p class="text-xs text-gray-400">User can log in</p></div>
                 <label class="toggle-switch"><input type="checkbox" name="status" checked><span class="toggle-slider"></span></label>
               </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="/bellavella/users/" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Create User</button>
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
