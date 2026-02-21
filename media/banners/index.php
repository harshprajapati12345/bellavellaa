<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Banners · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; } .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .banner-card { transition: all 0.2s; } .banner-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e5e7eb; border-radius: 999px; transition: 0.25s; }
    .toggle-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.25s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .img-preview { display: none; } .img-preview.show { display: block; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Banners'; include '../../includes/header.php'; ?>

    <?php
    $banners = [
      ['id'=>1,'title'=>'Summer Sale — Up to 50% Off','page'=>'Home','position'=>'Hero','image'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1200&q=80','status'=>'Active','clicks'=>1240,'start'=>'2024-06-01','end'=>'2024-06-30'],
      ['id'=>2,'title'=>'Bridal Season Special','page'=>'Home','position'=>'Banner 2','image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=1200&q=80','status'=>'Active','clicks'=>876,'start'=>'2024-05-15','end'=>'2024-07-15'],
      ['id'=>3,'title'=>'New Services — Explore Now','page'=>'Services','position'=>'Top','image'=>'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1200&q=80','status'=>'Inactive','clicks'=>342,'start'=>'2024-04-01','end'=>'2024-04-30'],
      ['id'=>4,'title'=>'Refer & Earn ₹500','page'=>'Dashboard','position'=>'Sidebar','image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=1200&q=80','status'=>'Active','clicks'=>2100,'start'=>'2024-01-01','end'=>'2024-12-31'],
    ];
    $total  = count($banners);
    $active = count(array_filter($banners, fn($b) => $b['status']==='Active'));
    $totalClicks = array_sum(array_column($banners, 'clicks'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Banners</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage promotional banners across the app</p>
        </div>
        <a href="../upload.php"
          class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 self-start sm:self-auto">
          <i data-lucide="plus" class="w-4 h-4"></i> Add Banner
        </a>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Banners</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="image" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900"><?php echo $active; ?></p><p class="text-xs text-gray-400 mt-0.5">Live now</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">Total Clicks</p><p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalClicks); ?></p><p class="text-xs text-gray-400 mt-0.5">All time</p></div>
          <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0"><i data-lucide="mouse-pointer-click" class="w-5 h-5 text-blue-500"></i></div>
        </div>
      </div>

      <!-- Banners Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <?php foreach($banners as $b): ?>
        <div class="banner-card bg-white rounded-2xl overflow-hidden shadow-[0_2px_16px_rgba(0,0,0,0.04)]">
          <div class="relative h-44">
            <img src="<?php echo $b['image']; ?>" class="w-full h-full object-cover" alt="">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute top-3 right-3">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm <?php echo $b['status']==='Active'?'bg-emerald-500/90 text-white':'bg-gray-500/80 text-white'; ?>">
                <?php echo $b['status']; ?>
              </span>
            </div>
            <div class="absolute bottom-3 left-4 right-4">
              <p class="text-white font-semibold text-sm leading-tight"><?php echo $b['title']; ?></p>
              <p class="text-white/70 text-xs mt-0.5"><?php echo $b['page']; ?> · <?php echo $b['position']; ?></p>
            </div>
          </div>
          <div class="p-4 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <div class="flex items-center gap-1.5"><i data-lucide="mouse-pointer-click" class="w-3.5 h-3.5 text-gray-400"></i><span><?php echo number_format($b['clicks']); ?> clicks</span></div>
              <div class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i><span><?php echo date('d M', strtotime($b['start'])); ?> – <?php echo date('d M', strtotime($b['end'])); ?></span></div>
            </div>
            <div class="flex items-center gap-2">
              <label class="toggle-switch"><input type="checkbox" <?php echo $b['status']==='Active'?'checked':''; ?> onchange="toggleBanner(<?php echo $b['id']; ?>, this)"><span class="toggle-slider"></span></label>
              <a href="../upload.php?id=<?php echo $b['id']; ?>" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
              </a>
              <a href="delete.php?id=<?php echo $b['id']; ?>" onclick="return confirm('Delete this banner?')" class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </main>
</div>



<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  function toggleBanner(id, el) {
    const s = el.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${s}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes' })
      .then(r => { if (!r.isConfirmed) el.checked = !el.checked; });
  }
  function deleteBanner(id) {
    Swal.fire({ title: 'Delete Banner?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) window.location.href = `delete.php?id=${id}`; });
  }
</script>
</body>
</html>
