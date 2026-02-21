<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Videos · Bellavella Admin</title>
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
    .video-card { transition: all 0.2s; } .video-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e5e7eb; border-radius: 999px; transition: 0.25s; }
    .toggle-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.25s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .play-btn { transition: all 0.2s; }
    .video-thumb:hover .play-btn { transform: scale(1.1); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Videos'; include '../../includes/header.php'; ?>

    <?php
    $videos = [
      ['id'=>1,'title'=>'Bridal Makeup Tutorial','category'=>'Makeup','thumbnail'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=800&q=80','duration'=>'12:34','views'=>8420,'status'=>'Published','uploaded'=>'2024-02-10'],
      ['id'=>2,'title'=>'Hair Styling Tips for Monsoon','category'=>'Hair','thumbnail'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80','duration'=>'8:15','views'=>5130,'status'=>'Published','uploaded'=>'2024-01-28'],
      ['id'=>3,'title'=>'Nail Art Step-by-Step Guide','category'=>'Nails','thumbnail'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=800&q=80','duration'=>'6:50','views'=>3900,'status'=>'Published','uploaded'=>'2024-01-15'],
      ['id'=>4,'title'=>'Skincare Routine for Professionals','category'=>'Skincare','thumbnail'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=800&q=80','duration'=>'15:22','views'=>2100,'status'=>'Draft','uploaded'=>'2024-02-18'],
      ['id'=>5,'title'=>'Party Glam Look in 10 Minutes','category'=>'Makeup','thumbnail'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=800&q=80','duration'=>'10:05','views'=>6780,'status'=>'Published','uploaded'=>'2024-02-05'],
      ['id'=>6,'title'=>'Spa & Massage Techniques','category'=>'Wellness','thumbnail'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80','duration'=>'20:00','views'=>1540,'status'=>'Draft','uploaded'=>'2024-02-17'],
    ];
    $total     = count($videos);
    $published = count(array_filter($videos, fn($v) => $v['status']==='Published'));
    $totalViews= array_sum(array_column($videos, 'views'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Videos</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage tutorial and promotional videos</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="vid-search" type="text" placeholder="Search videos…" oninput="filterVideos()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-48 transition-all">
          </div>
          <select id="vid-category" onchange="filterVideos()" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
            <option value="">All Categories</option>
            <option>Makeup</option><option>Hair</option><option>Nails</option><option>Skincare</option><option>Wellness</option>
          </select>
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Video
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Videos</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="video" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Published</p><p class="text-3xl font-bold text-gray-900"><?php echo $published; ?></p><p class="text-xs text-gray-400 mt-0.5">Live now</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">Total Views</p><p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalViews); ?></p><p class="text-xs text-gray-400 mt-0.5">All time</p></div>
          <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0"><i data-lucide="play-circle" class="w-5 h-5 text-blue-500"></i></div>
        </div>
      </div>

      <!-- Videos Grid -->
      <div id="videos-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        <?php foreach($videos as $v): ?>
        <div class="video-card bg-white rounded-2xl overflow-hidden shadow-[0_2px_16px_rgba(0,0,0,0.04)]"
             data-title="<?php echo strtolower($v['title']); ?>"
             data-category="<?php echo $v['category']; ?>">
          <div class="relative h-44 video-thumb cursor-pointer" onclick="playVideo('<?php echo $v['title']; ?>')">
            <img src="<?php echo $v['thumbnail']; ?>" class="w-full h-full object-cover" alt="">
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
              <div class="play-btn w-12 h-12 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                <i data-lucide="play" class="w-5 h-5 text-gray-900 ml-0.5"></i>
              </div>
            </div>
            <div class="absolute bottom-2 right-2 bg-black/70 text-white text-xs font-medium px-2 py-0.5 rounded-md"><?php echo $v['duration']; ?></div>
            <div class="absolute top-2 left-2">
              <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?php echo $v['status']==='Published'?'bg-emerald-500/90 text-white':'bg-gray-500/80 text-white'; ?>">
                <?php echo $v['status']; ?>
              </span>
            </div>
          </div>
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <div>
                <p class="text-sm font-semibold text-gray-900 leading-tight"><?php echo $v['title']; ?></p>
                <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full mt-1 inline-block"><?php echo $v['category']; ?></span>
              </div>
            </div>
            <div class="flex items-center justify-between mt-3">
              <div class="flex items-center gap-3 text-xs text-gray-400">
                <div class="flex items-center gap-1"><i data-lucide="play-circle" class="w-3.5 h-3.5"></i><span><?php echo number_format($v['views']); ?></span></div>
                <div class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5"></i><span><?php echo date('d M Y', strtotime($v['uploaded'])); ?></span></div>
              </div>
              <div class="flex items-center gap-1.5">
                <a href="edit.php?id=<?php echo $v['id']; ?>" class="w-7 h-7 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                  <i data-lucide="pencil" class="w-3 h-3"></i>
                </a>
                <a href="delete.php?id=<?php echo $v['id']; ?>" onclick="return confirm('Delete this video?')" class="w-7 h-7 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                  <i data-lucide="trash-2" class="w-3 h-3"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Empty state -->
      <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)]">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto"><i data-lucide="video-off" class="w-8 h-8 text-gray-300"></i></div>
        <p class="text-gray-500 font-medium">No videos found</p>
      </div>

    </div>
  </main>
</div>



<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  function filterVideos() {
    const search = document.getElementById('vid-search').value.toLowerCase();
    const cat = document.getElementById('vid-category').value;
    const cards = document.querySelectorAll('#videos-grid .video-card');
    let visible = 0;
    cards.forEach(c => {
      const match = c.dataset.title.includes(search) && (!cat || c.dataset.category === cat);
      c.style.display = match ? '' : 'none';
      if (match) visible++;
    });
    const empty = document.getElementById('empty-state');
    if (visible === 0) { empty.classList.remove('hidden'); empty.classList.add('flex'); }
    else { empty.classList.add('hidden'); empty.classList.remove('flex'); }
  }

  function playVideo(title) {
    Swal.fire({ title: 'Play Video', text: `Playing: "${title}"`, icon: 'info', confirmButtonColor: '#000', confirmButtonText: 'Close' });
  }
  function deleteVideo(id) {
    Swal.fire({ title: 'Delete Video?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) window.location.href = `delete.php?id=${id}`; });
  }
</script>
</body>
</html>
