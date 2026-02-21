<?php
/**
 * users/view.php — View User Profile
 */

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/users/'); exit; }

// TODO: $user = db_row('SELECT * FROM users WHERE id = ?', [$id]);
$user = ['id'=>$id,'name'=>'Ananya Kapoor','email'=>'ananya@example.com','phone'=>'+91 99887 76655','city'=>'Delhi','status'=>'Active','joined'=>'2023-08-20','orders'=>12,'spent'=>24800,'avatar'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'];
$pageTitle = 'User Profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['name']); ?> · Bellavella Admin</title>
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
    <div class="flex flex-col gap-6 max-w-2xl">
      <div class="flex items-center gap-4">
        <a href="/bellavella/users/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1"><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">User Profile</h2><p class="text-sm text-gray-400 mt-0.5">#<?php echo $id; ?></p></div>
        <a href="/bellavella/users/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this user?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i> Delete</a>
      </div>

      <div class="bg-white rounded-[2rem] shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <!-- Hero -->
        <div class="bg-gradient-to-r from-gray-900 to-gray-700 px-8 py-8 flex items-center gap-5">
          <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white/20" alt="">
          <div>
            <h3 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($user['name']); ?></h3>
            <p class="text-gray-300 text-sm mt-0.5"><?php echo htmlspecialchars($user['email']); ?></p>
            <span class="badge badge-green mt-2 inline-block"><?php echo htmlspecialchars($user['status']); ?></span>
          </div>
        </div>
        <!-- Stats -->
        <div class="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100">
          <div class="px-6 py-4 text-center"><p class="text-2xl font-bold text-gray-900"><?php echo $user['orders']; ?></p><p class="text-xs text-gray-400 mt-0.5">Orders</p></div>
          <div class="px-6 py-4 text-center"><p class="text-2xl font-bold text-gray-900">₹<?php echo number_format($user['spent']); ?></p><p class="text-xs text-gray-400 mt-0.5">Total Spent</p></div>
        </div>
        <!-- Details -->
        <div class="px-8 py-6 grid grid-cols-2 gap-5">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Phone</p><p class="text-sm text-gray-900"><?php echo htmlspecialchars($user['phone']); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">City</p><p class="text-sm text-gray-900"><?php echo htmlspecialchars($user['city']); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Joined</p><p class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($user['joined'])); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Status</p><span class="badge badge-green"><?php echo htmlspecialchars($user['status']); ?></span></div>
        </div>
      </div>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });</script>
</body>
</html>
