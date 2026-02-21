<?php
$pageTitle = 'Categories';

/* ── Parent Categories ──────────────────────────────────────────────────── */
$parents = [
  [
    'id'    => 1,
    'name'  => 'Luxe',
    'slug'  => 'luxe',
    'desc'  => 'Premium, high-end beauty services for discerning clients.',
    'color' => '#7c3aed',
    'image' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=600&q=80',
  ],
  [
    'id'    => 2,
    'name'  => 'Prime',
    'slug'  => 'prime',
    'desc'  => 'Affordable, everyday beauty and grooming essentials.',
    'color' => '#0891b2',
    'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80',
  ],
];

/* ── Subcategories ──────────────────────────────────────────────────────── */
$subcategories = [
  // Luxe (parent_id = 1)
  ['id'=>10,'parent_id'=>1,'name'=>'Bridal',       'slug'=>'bridal',       'services'=>14,'bookings'=>342,'status'=>'Active',  'featured'=>true, 'created'=>'2024-01-10','color'=>'#be185d','image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','desc'=>'Comprehensive bridal beauty packages for the most important day.'],
  ['id'=>11,'parent_id'=>1,'name'=>'Spa & Wellness','slug'=>'spa-wellness', 'services'=>8, 'bookings'=>189,'status'=>'Active',  'featured'=>true, 'created'=>'2024-01-15','color'=>'#1d4ed8','image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=400&q=80','desc'=>'Relaxing spa treatments and wellness rituals for mind and body.'],
  ['id'=>12,'parent_id'=>1,'name'=>'Makeup',        'slug'=>'makeup',       'services'=>9, 'bookings'=>267,'status'=>'Active',  'featured'=>true, 'created'=>'2024-02-10','color'=>'#7c3aed','image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=400&q=80','desc'=>'Expert makeup artistry for parties, events, and everyday glam.'],
  ['id'=>13,'parent_id'=>1,'name'=>'Facial',        'slug'=>'facial',       'services'=>6, 'bookings'=>210,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-05','color'=>'#db2777','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','desc'=>'Luxury facial treatments using premium skincare products.'],
  // Prime (parent_id = 2)
  ['id'=>20,'parent_id'=>2,'name'=>'Hair Care',     'slug'=>'hair-care',    'services'=>7, 'bookings'=>155,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-01','color'=>'#0891b2','image'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80','desc'=>'Complete hair care from cuts and styling to treatments.'],
  ['id'=>21,'parent_id'=>2,'name'=>'Hair Color',    'slug'=>'hair-color',   'services'=>5, 'bookings'=>132,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-10','color'=>'#d97706','image'=>'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=400&q=80','desc'=>'Professional hair colouring, highlights, and balayage.'],
  ['id'=>22,'parent_id'=>2,'name'=>'Nail Art',      'slug'=>'nail-art',     'services'=>5, 'bookings'=>98, 'status'=>'Inactive','featured'=>false,'created'=>'2024-02-20','color'=>'#e11d48','image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80','desc'=>'Creative nail art, gel extensions, and nail care treatments.'],
  ['id'=>23,'parent_id'=>2,'name'=>'Grooming',      'slug'=>'grooming',     'services'=>6, 'bookings'=>520,'status'=>'Active',  'featured'=>false,'created'=>'2024-02-01','color'=>'#15803d','image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=400&q=80','desc'=>'Professional grooming services for a sharp, polished look.'],
  ['id'=>24,'parent_id'=>2,'name'=>'Skin Care',     'slug'=>'skin-care',    'services'=>11,'bookings'=>411,'status'=>'Active',  'featured'=>false,'created'=>'2024-01-20','color'=>'#a16207','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','desc'=>'Advanced skin care treatments using the latest technology.'],
];

/* ── Aggregate stats per parent ─────────────────────────────────────────── */
foreach ($parents as &$p) {
  $children = array_filter($subcategories, fn($s) => $s['parent_id'] === $p['id']);
  $p['sub_count']    = count($children);
  $p['svc_count']    = array_sum(array_column(array_values($children), 'services'));
  $p['booking_count']= array_sum(array_column(array_values($children), 'bookings'));
}
unset($p);

$totalSubs     = count($subcategories);
$totalSvcs     = array_sum(array_column($subcategories, 'services'));
$totalBookings = array_sum(array_column($subcategories, 'bookings'));
$totalActive   = count(array_filter($subcategories, fn($s) => $s['status'] === 'Active'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories · Bellavella Admin</title>
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
    .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e5e7eb; border-radius: 999px; transition: 0.25s; }
    .toggle-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.25s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .parent-card { transition: all 0.2s; cursor: pointer; border: 2px solid transparent; }
    .parent-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
    .parent-card.active { border-color: #000; box-shadow: 0 8px 30px rgba(0,0,0,0.10); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Categories'; include '../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- ── Page Header ──────────────────────────────────────────────────── -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Categories</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage parent categories and their subcategories</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
          </a>
        </div>
      </div>

      <!-- ── Stat Cards ───────────────────────────────────────────────────── -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Parents</p><p class="text-3xl font-bold text-gray-900"><?php echo count($parents); ?></p><p class="text-xs text-gray-400 mt-0.5">Luxe &amp; Prime</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="folder" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Subcategories</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalSubs; ?></p><p class="text-xs text-gray-400 mt-0.5"><?php echo $totalActive; ?> active</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="folder-open" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Services</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalSvcs; ?></p><p class="text-xs text-gray-400 mt-0.5">Across all</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="store" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Bookings</p><p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalBookings); ?></p><p class="text-xs text-gray-400 mt-0.5">Total</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="calendar-check" class="w-5 h-5 text-gray-600"></i></div>
        </div>
      </div>

      <!-- ── Two-Panel Layout ─────────────────────────────────────────────── -->
      <div class="flex flex-col lg:flex-row gap-5 items-start">

        <!-- LEFT: Parent Category Cards -->
        <div class="w-full lg:w-72 flex-shrink-0 flex flex-col gap-3">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-1">Parent Categories</p>

          <!-- All button -->
          <button id="parent-all" onclick="filterByParent('all')"
            class="parent-card active w-full bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center gap-4 text-left">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100">
              <i data-lucide="layers" class="w-5 h-5 text-gray-600"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-900">All</p>
              <p class="text-xs text-gray-400 mt-0.5"><?php echo $totalSubs; ?> subcategories</p>
            </div>
          </button>

          <?php foreach($parents as $par): ?>
          <button id="parent-<?php echo $par['id']; ?>" onclick="filterByParent(<?php echo $par['id']; ?>)"
            class="parent-card w-full bg-white rounded-2xl overflow-hidden shadow-[0_2px_16px_rgba(0,0,0,0.04)] text-left">
            <!-- Cover image strip -->
            <div class="h-20 relative overflow-hidden">
              <img src="<?php echo $par['image']; ?>" class="w-full h-full object-cover" alt="">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              <div class="absolute bottom-3 left-4 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:<?php echo $par['color']; ?>"></span>
                <span class="text-white text-sm font-bold tracking-wide"><?php echo $par['name']; ?></span>
              </div>
            </div>
            <div class="p-4">
              <p class="text-xs text-gray-500 leading-relaxed line-clamp-2"><?php echo $par['desc']; ?></p>
              <div class="flex items-center gap-4 mt-3">
                <div class="text-center">
                  <p class="text-base font-bold text-gray-900"><?php echo $par['sub_count']; ?></p>
                  <p class="text-xs text-gray-400">Subcats</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="text-center">
                  <p class="text-base font-bold text-gray-900"><?php echo $par['svc_count']; ?></p>
                  <p class="text-xs text-gray-400">Services</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="text-center">
                  <p class="text-base font-bold text-gray-900"><?php echo number_format($par['booking_count']); ?></p>
                  <p class="text-xs text-gray-400">Bookings</p>
                </div>
              </div>
            </div>
          </button>
          <?php endforeach; ?>

          <a href="create.php?type=parent"
            class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl border-2 border-dashed border-gray-200 text-sm text-gray-400 hover:border-gray-400 hover:text-gray-600 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Parent
          </a>
        </div>

        <!-- RIGHT: Subcategories Table -->
        <div class="flex-1 min-w-0 flex flex-col gap-4">

          <!-- Toolbar -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="relative">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input id="cat-search" type="text" placeholder="Search subcategories…" oninput="applyFilters()"
                  class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
              </div>
              <select id="f-status" onchange="applyFilters()"
                class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer transition-all">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            <div id="active-parent-label" class="text-sm font-medium text-gray-500 hidden">
              Showing: <span id="active-parent-name" class="text-gray-900 font-semibold"></span>
            </div>
          </div>

          <!-- Table -->
          <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full min-w-[640px]">
                <thead>
                  <tr class="border-b border-gray-100 bg-gray-50/80">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Subcategory</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Parent</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Services</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                  </tr>
                </thead>
                <tbody id="categories-tbody">
                  <?php
                  $parentMap = array_column($parents, 'name', 'id');
                  $parentColorMap = array_column($parents, 'color', 'id');
                  foreach($subcategories as $cat):
                  ?>
                  <tr class="table-row border-b border-gray-50"
                      data-name="<?php echo strtolower($cat['name']); ?>"
                      data-parent="<?php echo $cat['parent_id']; ?>"
                      data-status="<?php echo $cat['status']; ?>">
                    <td class="px-5 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0">
                          <img src="<?php echo $cat['image']; ?>" class="w-full h-full object-cover" alt="">
                        </div>
                        <div>
                          <p class="text-sm font-semibold text-gray-900"><?php echo $cat['name']; ?></p>
                          <p class="text-xs text-gray-400 mt-0.5">/<?php echo $cat['slug']; ?></p>
                        </div>
                      </div>
                    </td>
                    <td class="px-5 py-4">
                      <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                        style="background:<?php echo $parentColorMap[$cat['parent_id']]; ?>18; color:<?php echo $parentColorMap[$cat['parent_id']]; ?>">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:<?php echo $parentColorMap[$cat['parent_id']]; ?>"></span>
                        <?php echo $parentMap[$cat['parent_id']]; ?>
                      </span>
                    </td>
                    <td class="px-5 py-4">
                      <span class="text-sm font-medium text-gray-900"><?php echo $cat['services']; ?></span>
                    </td>
                    <td class="px-5 py-4">
                      <span class="text-sm font-medium text-gray-900"><?php echo number_format($cat['bookings']); ?></span>
                    </td>
                    <td class="px-5 py-4">
                      <label class="toggle-switch">
                        <input type="checkbox" <?php echo $cat['status']==='Active'?'checked':''; ?> onchange="toggleStatus(<?php echo $cat['id']; ?>, this)">
                        <span class="toggle-slider"></span>
                      </label>
                    </td>
                    <td class="px-5 py-4">
                      <div class="flex items-center justify-end gap-1.5">
                        <button onclick='openDrawer(<?php echo json_encode($cat); ?>)' title="View"
                          class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                          <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        </button>
                        <a href="edit.php?id=<?php echo $cat['id']; ?>" title="Edit"
                          class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                          <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="delete.php?id=<?php echo $cat['id']; ?>" title="Delete"
                          onclick="return confirm('Delete <?php echo addslashes($cat['name']); ?>? This cannot be undone.')"
                          class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                          <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Empty state -->
            <div id="empty-state" class="hidden flex-col items-center justify-center py-16 text-center">
              <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                <i data-lucide="folder-x" class="w-7 h-7 text-gray-300"></i>
              </div>
              <p class="text-gray-500 font-medium">No subcategories found</p>
              <p class="text-gray-400 text-sm mt-1">Try adjusting your filter</p>
            </div>

            <!-- Pagination -->
            <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
              <p id="pagination-info" class="text-sm text-gray-400"></p>
              <div id="pagination-btns" class="flex items-center gap-1.5"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- ── VIEW DRAWER ─────────────────────────────────────────────────────────── -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">

  <!-- Cover image -->
  <div class="relative h-44 flex-shrink-0">
    <img id="d-image" src="" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
    <button onclick="closeDrawer()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 backdrop-blur-md hover:bg-white/40 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-white"></i>
    </button>
    <div class="absolute bottom-4 left-5 right-5">
      <h3 id="d-name" class="text-xl font-bold text-white"></h3>
      <p id="d-slug" class="text-sm text-white/70 mt-0.5"></p>
    </div>
    <span id="d-status-badge" class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md"></span>
  </div>

  <!-- Body -->
  <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-5">
    <!-- Parent badge -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Parent Category</p>
      <span id="d-parent-badge" class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-full"></span>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-gray-50 rounded-2xl p-4 text-center">
        <p id="d-services" class="text-2xl font-bold text-gray-900"></p>
        <p class="text-xs text-gray-400 mt-0.5">Services</p>
      </div>
      <div class="bg-gray-50 rounded-2xl p-4 text-center">
        <p id="d-bookings" class="text-2xl font-bold text-gray-900"></p>
        <p class="text-xs text-gray-400 mt-0.5">Bookings</p>
      </div>
    </div>

    <!-- Description -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Description</p>
      <p id="d-desc" class="text-sm text-gray-600 leading-relaxed"></p>
    </div>

    <!-- Created -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Created</p>
      <p id="d-created" class="text-sm text-gray-600"></p>
    </div>
  </div>

  <!-- Footer -->
  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <a id="d-edit-btn" href="#"
      class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
      <i data-lucide="pencil" class="w-4 h-4"></i> Edit
    </a>
    <a id="d-delete-btn" href="#"
      class="w-12 h-12 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
      <i data-lucide="trash-2" class="w-4 h-4"></i>
    </a>
  </div>
</div>

<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  // ── Parent filter ─────────────────────────────────────────────────────────
  let activeParent = 'all';

  const parentNames = {
    <?php foreach($parents as $par): ?>
    <?php echo $par['id']; ?>: '<?php echo $par['name']; ?>',
    <?php endforeach; ?>
  };
  const parentColors = {
    <?php foreach($parents as $par): ?>
    <?php echo $par['id']; ?>: '<?php echo $par['color']; ?>',
    <?php endforeach; ?>
  };

  function filterByParent(id) {
    activeParent = id;
    // Update card active states
    document.getElementById('parent-all').classList.toggle('active', id === 'all');
    <?php foreach($parents as $par): ?>
    document.getElementById('parent-<?php echo $par['id']; ?>').classList.toggle('active', id === <?php echo $par['id']; ?>);
    <?php endforeach; ?>
    // Update label
    const lbl = document.getElementById('active-parent-label');
    const nm  = document.getElementById('active-parent-name');
    if (id === 'all') { lbl.classList.add('hidden'); }
    else { lbl.classList.remove('hidden'); nm.textContent = parentNames[id]; }
    applyFilters();
  }

  // ── Pagination ────────────────────────────────────────────────────────────
  const ROWS_PER_PAGE = 6;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search = document.getElementById('cat-search').value.toLowerCase();
    const status = document.getElementById('f-status').value;
    const allRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const nameMatch   = row.dataset.name.includes(search);
      const parentMatch = activeParent === 'all' || row.dataset.parent == activeParent;
      const statusMatch = !status || row.dataset.status === status;
      return nameMatch && parentMatch && statusMatch;
    });
    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE, end = start + ROWS_PER_PAGE;
    visibleRows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
    const empty = document.getElementById('empty-state'), pw = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) { empty.classList.remove('hidden'); empty.classList.add('flex'); pw.classList.add('hidden'); }
    else { empty.classList.add('hidden'); empty.classList.remove('flex'); pw.classList.remove('hidden'); }
    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length, totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total), end = Math.min(currentPage * ROWS_PER_PAGE, total);
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} subcategor${total !== 1 ? 'ies' : 'y'}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    const mkArrow = (svg, disabled, onClick) => { const b = document.createElement('button'); b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed'; b.innerHTML = svg; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mkArrow('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage===1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mkArrow('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }));
  }

  // ── Toggle Status ─────────────────────────────────────────────────────────
  function toggleStatus(id, checkbox) {
    const newStatus = checkbox.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${newStatus}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, ${newStatus}` })
      .then(r => {
        if (!r.isConfirmed) checkbox.checked = !checkbox.checked;
        else Swal.fire({ title: 'Updated!', text: `Category is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
      });
  }

  // ── Drawer ────────────────────────────────────────────────────────────────
  function openDrawer(cat) {
    document.getElementById('d-image').src   = cat.image;
    document.getElementById('d-name').textContent  = cat.name;
    document.getElementById('d-slug').textContent  = '/' + cat.slug;
    document.getElementById('d-services').textContent = cat.services;
    document.getElementById('d-bookings').textContent = Number(cat.bookings).toLocaleString('en-IN');
    document.getElementById('d-desc').textContent  = cat.desc;

    const d = new Date(cat.created);
    document.getElementById('d-created').textContent = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });

    const sb = document.getElementById('d-status-badge');
    sb.textContent = cat.status;
    sb.className = 'absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md ' +
      (cat.status === 'Active' ? 'bg-white/90 text-emerald-600' : 'bg-gray-200/90 text-gray-500');

    const pb = document.getElementById('d-parent-badge');
    const pColor = parentColors[cat.parent_id];
    const pName  = parentNames[cat.parent_id];
    pb.textContent = pName;
    pb.style.background = pColor + '18';
    pb.style.color = pColor;

    document.getElementById('d-edit-btn').href = `edit.php?id=${cat.id}`;
    document.getElementById('d-delete-btn').href = `delete.php?id=${cat.id}`;
    document.getElementById('d-delete-btn').onclick = (e) => {
      e.preventDefault();
      Swal.fire({ title: 'Delete Category?', text: 'All associated services may be affected.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
        .then(r => { if (r.isConfirmed) window.location.href = `delete.php?id=${cat.id}`; });
    };

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeDrawer() {
    document.getElementById('drawer-panel').classList.add('closed');
    document.getElementById('drawer-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
  }

  // ── Init ──────────────────────────────────────────────────────────────────
  (function init() { visibleRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
