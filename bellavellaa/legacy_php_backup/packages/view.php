<?php
/**
 * packages/view.php — View Package Details
 * Usage: /bella/packages/view.php?id=1
 */


$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/packages/'); exit; }

// TODO: $package = db_row('SELECT * FROM packages WHERE id = ?', [$id]);
$package = [
  'id' => $id, 'name' => 'Bridal Glow Package', 'category' => 'Bridal',
  'price' => 4999, 'discount' => 10, 'duration' => '3 hrs',
  'services' => 'Facial, Manicure, Blowout', 'description' => 'A complete bridal prep package for the big day. Includes a deep cleansing facial, gel manicure, and professional blowout.',
  'status' => 1, 'featured' => 1, 'bookings' => 48, 'rating' => 4.8,
  'image' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=800&q=80',
  'created_at' => '2024-01-15', 'created_by' => 'Admin',
];

$pageTitle = 'View Package';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($package['name']); ?> · Bellavella Admin</title>
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
        <a href="/bella/packages/" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight"><?php echo htmlspecialchars($package['name']); ?></h2>
          <p class="text-sm text-gray-400 mt-0.5">Package #<?php echo $id; ?></p>
        </div>
        <div class="flex items-center gap-2">
          <a href="/bella/packages/edit.php?id=<?php echo $id; ?>" class="btn btn-primary text-sm">
            <i data-lucide="pencil" class="w-4 h-4"></i> Edit
          </a>
          <a href="/bella/packages/delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this package?')" class="btn btn-danger text-sm">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </a>
        </div>
      </div>

      <!-- Image + Status -->
      <div class="bg-white rounded-[2rem] overflow-hidden shadow-[0_2px_16px_rgba(0,0,0,0.04)]">
        <div class="relative h-56">
          <img src="<?php echo htmlspecialchars($package['image']); ?>" class="w-full h-full object-cover" alt="">
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          <div class="absolute top-4 right-4 flex gap-2">
            <span class="badge <?php echo $package['status'] ? 'badge-green' : 'badge-gray'; ?>">
              <?php echo $package['status'] ? 'Active' : 'Inactive'; ?>
            </span>
            <?php if ($package['featured']): ?>
            <span class="badge badge-yellow">⭐ Featured</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 border-b border-gray-100">
          <div class="px-6 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">₹<?php echo number_format($package['price']); ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Price</p>
          </div>
          <div class="px-6 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo $package['bookings']; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Bookings</p>
          </div>
          <div class="px-6 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo $package['rating']; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Rating</p>
          </div>
        </div>

        <!-- Details -->
        <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Category</p>
            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($package['category']); ?></p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Duration</p>
            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($package['duration']); ?></p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Discount</p>
            <p class="text-sm font-medium text-gray-900"><?php echo $package['discount']; ?>%</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Created</p>
            <p class="text-sm font-medium text-gray-900"><?php echo date('d M Y', strtotime($package['created_at'])); ?></p>
          </div>
          <div class="col-span-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Services Included</p>
            <div class="flex flex-wrap gap-2 mt-1">
              <?php foreach(explode(',', $package['services']) as $svc): ?>
              <span class="badge badge-blue"><?php echo trim($svc); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="col-span-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Description</p>
            <p class="text-sm text-gray-600 leading-relaxed"><?php echo htmlspecialchars($package['description']); ?></p>
          </div>
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
