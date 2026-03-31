<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Media Manager · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-black-text, .sidebar-black-text span, .sidebar-black-text i,
    .sidebar-black-text a span, .sidebar-black-text button span { color: #000000 !important; }
    .sidebar-black-text [data-lucide] { color: #000000 !important; opacity: 0.8; transition: opacity 0.2s; }
    .sidebar-black-text a:hover [data-lucide], .sidebar-black-text button:hover [data-lucide] { opacity: 1; }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .table-row { transition: background 0.15s; }
    .table-row:hover { background: #fafafa; }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; cursor: pointer; inset: 0;
      background: #e5e7eb; border-radius: 999px; transition: 0.25s;
    }
    .toggle-slider:before {
      content: ''; position: absolute;
      width: 16px; height: 16px; left: 3px; bottom: 3px;
      background: white; border-radius: 50%; transition: 0.25s;
      box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .page-btn { transition: all 0.15s; }
    .page-btn:hover { background: #000; color: #fff; }
    .page-btn.active { background: #000; color: #fff; }
    .media-preview {
      width: 56px; height: 56px; object-fit: cover; border-radius: 12px;
      background: #f3f4f6;
    }
  </style>
</head>
<body>
<?php $pageTitle = 'Media Manager'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="lg:ml-72 min-h-screen p-6 sm:p-10">
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <!-- Stats Row -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Media</p>
      <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Images</p>
      <p class="text-2xl font-bold text-blue-600 mt-1">9</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Videos</p>
      <p class="text-2xl font-bold text-purple-600 mt-1">3</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Active</p>
      <p class="text-2xl font-bold text-emerald-600 mt-1">10</p>
    </div>
  </div>

  <!-- Filters & Actions -->
  <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-5 sm:p-6 mb-6">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
        <!-- Search -->
        <div class="relative w-full sm:w-60">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
          <input type="text" placeholder="Search media..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
        <!-- Filter by type -->
        <select class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 bg-white">
          <option value="">All Types</option>
          <option>Image</option>
          <option>Video</option>
        </select>
        <!-- Filter by section -->
        <select class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 bg-white">
          <option value="">All Sections</option>
          <option>Hero Banner</option>
          <option>Gallery</option>
          <option>About</option>
          <option>Services</option>
        </select>
      </div>

      <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
        <a href="/bella/media/banners/" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
          <i data-lucide="image" class="w-4 h-4"></i>
          Banners
        </a>
        <a href="/bella/media/videos/" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
          <i data-lucide="video" class="w-4 h-4"></i>
          Videos
        </a>
        <a href="create.php" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-black text-white px-6 py-2.5 rounded-xl hover:bg-gray-800 transition-all font-medium text-sm shadow-sm">
          <i data-lucide="plus" class="w-4 h-4"></i>
          Add Media
        </a>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">ID</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Preview</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Title</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Linked Section</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $media = [
            ['id'=>1,  'title'=>'Hero Slide 1',        'type'=>'Image', 'section'=>'Hero Banner', 'order'=>1, 'status'=>true],
            ['id'=>2,  'title'=>'Hero Slide 2',        'type'=>'Image', 'section'=>'Hero Banner', 'order'=>2, 'status'=>true],
            ['id'=>3,  'title'=>'Hero Video',           'type'=>'Video', 'section'=>'Hero Banner', 'order'=>3, 'status'=>true],
            ['id'=>4,  'title'=>'Salon Interior',       'type'=>'Image', 'section'=>'About',       'order'=>1, 'status'=>true],
            ['id'=>5,  'title'=>'Bridal Showcase',      'type'=>'Image', 'section'=>'Gallery',     'order'=>1, 'status'=>true],
            ['id'=>6,  'title'=>'Hair Treatment Demo',  'type'=>'Video', 'section'=>'Gallery',     'order'=>2, 'status'=>false],
            ['id'=>7,  'title'=>'Nail Art Gallery',     'type'=>'Image', 'section'=>'Gallery',     'order'=>3, 'status'=>true],
            ['id'=>8,  'title'=>'Spa Ambience',         'type'=>'Image', 'section'=>'Gallery',     'order'=>4, 'status'=>true],
            ['id'=>9,  'title'=>'Team Photo',           'type'=>'Image', 'section'=>'About',       'order'=>2, 'status'=>true],
            ['id'=>10, 'title'=>'Promo Reel',           'type'=>'Video', 'section'=>'Hero Banner', 'order'=>4, 'status'=>false],
            ['id'=>11, 'title'=>'Services Banner',      'type'=>'Image', 'section'=>'Services',    'order'=>1, 'status'=>true],
            ['id'=>12, 'title'=>'Makeup Tutorial',      'type'=>'Image', 'section'=>'Gallery',     'order'=>5, 'status'=>true],
          ];
          foreach ($media as $m):
            $typeBg = $m['type'] === 'Image' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600';
            $typeIcon = $m['type'] === 'Image' ? 'image' : 'video';
          ?>
          <tr class="table-row border-b border-gray-50">
            <td class="px-6 py-4 text-sm text-gray-400 font-medium">#<?php echo $m['id']; ?></td>
            <td class="px-4 py-4">
              <div class="media-preview flex items-center justify-center bg-gray-100">
                <i data-lucide="<?php echo $typeIcon; ?>" class="w-5 h-5 text-gray-400"></i>
              </div>
            </td>
            <td class="px-4 py-4">
              <span class="text-sm font-semibold text-gray-900"><?php echo $m['title']; ?></span>
            </td>
            <td class="px-4 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-lg <?php echo $typeBg; ?>"><?php echo $m['type']; ?></span>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500"><?php echo $m['section']; ?></td>
            <td class="px-4 py-4 text-sm text-gray-400 text-center"><?php echo $m['order']; ?></td>
            <td class="px-4 py-4">
              <label class="toggle-switch">
                <input type="checkbox" <?php echo $m['status'] ? 'checked' : ''; ?>>
                <span class="toggle-slider"></span>
              </label>
            </td>
            <td class="px-4 py-4 text-right">
              <div class="flex items-center justify-end gap-1">
                <a href="/bella/media/edit.php?id=<?php echo $m['id']; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" title="Edit">
                  <i data-lucide="pencil" class="w-4 h-4"></i>
                </a>
                <button onclick="confirmDelete('#', '<?php echo $m['title']; ?>')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row items-center justify-between px-8 py-5 border-t border-gray-100 gap-3">
      <p class="text-sm text-gray-400">Showing <strong class="text-gray-700">1–12</strong> of <strong class="text-gray-700">12</strong> results</p>
      <div class="flex items-center gap-1">
        <button class="page-btn w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 text-sm" disabled>
          <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>
        <button class="page-btn active w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium">1</button>
        <button class="page-btn w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 text-sm">
          <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
</script>
</body>
</html>
