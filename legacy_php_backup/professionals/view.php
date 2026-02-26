<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/professionals/'); exit; }
$pro = ['id'=>$id,'name'=>'Priya Sharma','email'=>'priya@example.com','phone'=>'+91 98765 43210','category'=>'Makeup Artist','city'=>'Mumbai','bio'=>'Expert bridal makeup artist with 8 years of experience.','status'=>1,'verified'=>1,'rating'=>4.9,'orders'=>124,'earnings'=>186000,'photo'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','joined'=>'2023-03-12'];
$pageTitle = 'Professional Profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pro['name']); ?> · Bellavella Admin</title>
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
    <div class="flex flex-col gap-6 max-w-3xl">
      <!-- Header -->
      <div class="flex items-center gap-4">
        <a href="/bella/professionals/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
        <div class="flex-1"><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professional Profile</h2><p class="text-sm text-gray-400 mt-0.5">#<?php echo $id; ?></p></div>
        <div class="flex gap-2">
          <a href="/bella/professionals/edit.php?id=<?php echo $id; ?>" class="btn btn-primary text-sm"><i data-lucide="pencil" class="w-4 h-4"></i> Edit</a>
          <a href="/bella/professionals/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete?')" class="btn btn-danger text-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
        </div>
      </div>

      <!-- Profile Card -->
      <div class="bg-white rounded-[2rem] shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <!-- Hero -->
        <div class="bg-gradient-to-r from-gray-900 to-gray-700 px-8 py-8 flex items-center gap-5">
          <img src="<?php echo htmlspecialchars($pro['photo']); ?>" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white/20" alt="">
          <div>
            <h3 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($pro['name']); ?></h3>
            <p class="text-gray-300 text-sm mt-0.5"><?php echo htmlspecialchars($pro['category']); ?> · <?php echo htmlspecialchars($pro['city']); ?></p>
            <div class="flex items-center gap-2 mt-2">
              <span class="badge <?php echo $pro['status'] ? 'badge-green' : 'badge-gray'; ?>"><?php echo $pro['status'] ? 'Active' : 'Inactive'; ?></span>
              <?php if ($pro['verified']): ?><span class="badge badge-blue">✓ Verified</span><?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
          <div class="px-6 py-4 text-center"><p class="text-2xl font-bold text-gray-900"><?php echo $pro['orders']; ?></p><p class="text-xs text-gray-400 mt-0.5">Orders</p></div>
          <div class="px-6 py-4 text-center"><p class="text-2xl font-bold text-gray-900">₹<?php echo number_format($pro['earnings']); ?></p><p class="text-xs text-gray-400 mt-0.5">Earnings</p></div>
          <div class="px-6 py-4 text-center"><p class="text-2xl font-bold text-gray-900"><?php echo $pro['rating']; ?></p><p class="text-xs text-gray-400 mt-0.5">Rating</p></div>
        </div>

        <!-- Details -->
        <div class="px-8 py-6 grid grid-cols-2 gap-5">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Email</p><p class="text-sm text-gray-900"><?php echo htmlspecialchars($pro['email']); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Phone</p><p class="text-sm text-gray-900"><?php echo htmlspecialchars($pro['phone']); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Joined</p><p class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($pro['joined'])); ?></p></div>
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Category</p><p class="text-sm text-gray-900"><?php echo htmlspecialchars($pro['category']); ?></p></div>
          <div class="col-span-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Bio</p><p class="text-sm text-gray-600 leading-relaxed"><?php echo htmlspecialchars($pro['bio']); ?></p></div>
        </div>
      </div>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });</script>
</body>
</html>
