<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Packages · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
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
    .bulk-bar { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
    .img-preview { display: none; } .img-preview.show { display: block; }
    .filter-bar { transition: all 0.2s; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Packages'; include '../includes/header.php'; ?>

    <?php
    $packages = [
      ['id'=>1,'name'=>'HD Bridal Makeup','category'=>'Luxe','services'=>['Gold Facial','Hair Styling','Manicure & Pedicure'],'price'=>12000,'discount'=>20,'duration'=>300,'bookings'=>48,'status'=>'Active','featured'=>true,'image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=800&q=80','description'=>'Complete bridal transformation package with premium gold facial, professional hair styling, and luxury manicure & pedicure.','created'=>'2023-08-15'],
      ['id'=>2,'name'=>'Weekend Rejuvenation','category'=>'Luxe','services'=>['Aromatherapy Massage','Deep Cleansing Facial'],'price'=>5000,'discount'=>0,'duration'=>120,'bookings'=>91,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=800&q=80','description'=>'Unwind with a soothing aromatherapy massage followed by a deep cleansing facial for glowing skin.','created'=>'2023-09-01'],
      ['id'=>3,'name'=>'Express Grooming','category'=>'Prime','services'=>['Haircut','Beard Trim','Express Facial'],'price'=>1500,'discount'=>5,'duration'=>60,'bookings'=>134,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=800&q=80','description'=>'Quick yet thorough grooming session covering haircut, beard trim, and an express facial.','created'=>'2023-10-10'],
      ['id'=>4,'name'=>'Party Glam','category'=>'Luxe','services'=>['Party Makeup','Blow Dry','Nail Art'],'price'=>4500,'discount'=>10,'duration'=>180,'bookings'=>62,'status'=>'Active','featured'=>true,'image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=800&q=80','description'=>'Get party-ready with a full glam makeup look, salon blow dry, and trendy nail art.','created'=>'2023-11-05'],
      ['id'=>5,'name'=>'Spa Bliss','category'=>'Luxe','services'=>['Swedish Massage','Hydra Facial','Foot Spa'],'price'=>7500,'discount'=>15,'duration'=>240,'bookings'=>29,'status'=>'Inactive','featured'=>false,'image'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80','description'=>'A full-day spa experience with Swedish massage, hydra facial, and relaxing foot spa.','created'=>'2024-01-20'],
      ['id'=>6,'name'=>'Nail Art Deluxe','category'=>'Prime','services'=>['Gel Nails','Nail Art','Cuticle Care'],'price'=>2000,'discount'=>0,'duration'=>90,'bookings'=>77,'status'=>'Active','featured'=>false,'image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=800&q=80','description'=>'Premium nail art session with gel nails, intricate designs, and cuticle care.','created'=>'2024-02-01'],
    ];
    $total    = count($packages);
    $active   = count(array_filter($packages, fn($p) => $p['status']==='Active'));
    $inactive = $total - $active;
    $topBooked = array_reduce($packages, fn($carry, $p) => $p['bookings'] > ($carry['bookings'] ?? 0) ? $p : $carry, []);
    ?>

    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Packages</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all beauty packages</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="pkg-search" type="text" placeholder="Search packages..." oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
          </div>
          <button id="filter-toggle" onclick="toggleFilterBar()"
            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all sm:hidden">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
          </button>
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Package
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Packages</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="package" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900"><?php echo $active; ?></p><p class="text-xs text-gray-400 mt-0.5">Live now</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Inactive</p><p class="text-3xl font-bold text-gray-900"><?php echo $inactive; ?></p><p class="text-xs text-gray-400 mt-0.5">Paused</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="pause-circle" class="w-5 h-5 text-gray-400"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Top Booked</p><p class="text-lg font-bold text-gray-900 leading-tight mt-0.5"><?php echo $topBooked['name']; ?></p><p class="text-xs text-gray-400 mt-0.5"><?php echo $topBooked['bookings']; ?> bookings</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-violet-500"></i></div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div id="filter-bar" class="filter-bar bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-3">
        <select id="f-category" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Categories</option>
          <option value="Luxe">Luxe</option>
          <option value="Prime">Prime</option>
        </select>
        <select id="f-status" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Statuses</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        <select id="f-price" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">Price</option>
          <option value="asc">Low → High</option>
          <option value="desc">High → Low</option>
        </select>
        <select id="f-duration" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">Duration</option>
          <option value="asc">Short → Long</option>
          <option value="desc">Long → Short</option>
        </select>
        <select id="f-featured" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All</option>
          <option value="1">Featured Only</option>
        </select>
        <button onclick="resetFilters()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-100 transition-all ml-auto">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
        </button>
      </div>

      <!-- Packages Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">

        <!-- Bulk Action Bar -->
        <div id="bulk-bar" class="bulk-bar hidden items-center gap-3 px-5 py-3 bg-gray-900 text-white">
          <span id="bulk-count" class="text-sm font-medium"></span>
          <button onclick="bulkDelete()" class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-all">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Selected
          </button>
          <button onclick="clearSelection()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-medium transition-all">
            <i data-lucide="x" class="w-4 h-4"></i> Cancel
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[1000px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left w-10"><input type="checkbox" id="select-all" onchange="toggleAll(this)" class="w-4 h-4 rounded border-gray-300 cursor-pointer accent-black"></th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Package</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Services</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Duration</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Created</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="pkg-tbody">
              <?php foreach($packages as $pkg):
                $finalPrice = $pkg['price'] - ($pkg['price'] * ($pkg['discount'] / 100));
                $hrs = intdiv($pkg['duration'], 60);
                $mins = $pkg['duration'] % 60;
                $durationLabel = ($hrs > 0 ? $hrs.'h ' : '') . ($mins > 0 ? $mins.'m' : '');
              ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $pkg['id']; ?>"
                  data-name="<?php echo strtolower($pkg['name']); ?>"
                  data-category="<?php echo $pkg['category']; ?>"
                  data-status="<?php echo $pkg['status']; ?>"
                  data-price="<?php echo $finalPrice; ?>"
                  data-duration="<?php echo $pkg['duration']; ?>"
                  data-featured="<?php echo $pkg['featured'] ? '1' : '0'; ?>">
                <td class="px-5 py-4"><input type="checkbox" class="row-check w-4 h-4 rounded border-gray-300 cursor-pointer accent-black" onchange="updateBulkBar()"></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $pkg['image']; ?>" class="w-10 h-10 rounded-xl object-cover flex-shrink-0" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?php echo $pkg['name']; ?></p>
                      <?php if($pkg['featured']): ?><span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Featured</span><?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $pkg['category']==='Luxe'?'bg-violet-50 text-violet-600':'bg-amber-50 text-amber-600'; ?>">
                    <?php echo $pkg['category']; ?>
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex flex-wrap gap-1 max-w-[200px]">
                    <?php foreach(array_slice($pkg['services'], 0, 2) as $svc): ?>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?php echo $svc; ?></span>
                    <?php endforeach; ?>
                    <?php if(count($pkg['services']) > 2): ?>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">+<?php echo count($pkg['services'])-2; ?></span>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $durationLabel; ?></td>
                <td class="px-5 py-4">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">₹<?php echo number_format($finalPrice, 0); ?></p>
                    <?php if($pkg['discount'] > 0): ?>
                    <p class="text-xs text-gray-400 line-through">₹<?php echo number_format($pkg['price'], 0); ?></p>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700"><?php echo $pkg['bookings']; ?></td>
                <td class="px-5 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" <?php echo $pkg['status']==='Active'?'checked':''; ?> onchange="toggleStatus(<?php echo $pkg['id']; ?>, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td class="px-5 py-4 text-sm text-gray-400"><?php echo date('d M Y', strtotime($pkg['created'])); ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="view.php?id=<?php echo $pkg['id']; ?>" title="View"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="edit.php?id=<?php echo $pkg['id']; ?>" title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="delete.php?id=<?php echo $pkg['id']; ?>" title="Delete" onclick="return confirm('Delete this package?')"
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

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <i data-lucide="package-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No packages found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters</p>
          <button onclick="resetFilters()" class="mt-4 px-5 py-2.5 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all">Reset Filters</button>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- -- VIEW DRAWER ------------------------------------------------------- -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">
  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <h3 class="text-lg font-semibold text-gray-900">Package Details</h3>
    <button onclick="closeDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>
  <div class="flex-1 overflow-y-auto">
    <div class="relative h-52 flex-shrink-0">
      <img id="d-image" src="" class="w-full h-full object-cover" alt="">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
      <div class="absolute bottom-4 left-5 right-5 flex items-end justify-between">
        <div>
          <h2 id="d-name" class="text-xl font-bold text-white leading-tight"></h2>
          <p id="d-category" class="text-sm text-white/70 mt-0.5"></p>
        </div>
        <div class="text-right">
          <p id="d-price" class="text-xl font-bold text-white"></p>
          <p id="d-original-price" class="text-sm text-white/60 line-through"></p>
        </div>
      </div>
    </div>
    <div class="p-6 flex flex-col gap-5">
      <!-- Quick stats -->
      <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 rounded-2xl p-4 text-center"><p id="d-duration" class="text-lg font-bold text-gray-900"></p><p class="text-xs text-gray-400 mt-0.5">Duration</p></div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center"><p id="d-bookings" class="text-lg font-bold text-gray-900"></p><p class="text-xs text-gray-400 mt-0.5">Bookings</p></div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center"><p id="d-discount" class="text-lg font-bold text-gray-900"></p><p class="text-xs text-gray-400 mt-0.5">Discount</p></div>
      </div>
      <!-- Status & Featured -->
      <div class="flex items-center gap-2">
        <span id="d-status-badge" class="text-xs font-semibold px-3 py-1.5 rounded-full"></span>
        <span id="d-featured-badge" class="hidden text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-50 text-amber-600 flex items-center gap-1">
          <i data-lucide="star" class="w-3 h-3 fill-amber-500 text-amber-500"></i> Featured
        </span>
      </div>
      <!-- Services -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Included Services</p>
        <div id="d-services" class="flex flex-wrap gap-2"></div>
      </div>
      <!-- Description -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Description</p>
        <p id="d-description" class="text-sm text-gray-600 leading-relaxed"></p>
      </div>
      <!-- Created -->
      <div class="flex items-center gap-2 text-sm text-gray-400">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>Created: <span id="d-created" class="text-gray-600 font-medium"></span></span>
      </div>
    </div>
  </div>
  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <a id="d-edit-btn" href="#" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
      <i data-lucide="pencil" class="w-4 h-4"></i> Edit Package
    </a>
    <a id="d-delete-btn" href="#" class="w-12 h-12 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
      <i data-lucide="trash-2" class="w-4 h-4"></i>
    </a>
  </div>
</div>



<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  // Sidebar toggles are handled by sidebar.php

  // -- Filters & Pagination ----------------------------------------------
  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [];

  function toggleFilterBar() {
    const fb = document.getElementById('filter-bar');
    fb.classList.toggle('hidden');
  }

  function applyFilters() {
    const search   = document.getElementById('pkg-search').value.toLowerCase();
    const category = document.getElementById('f-category').value;
    const status   = document.getElementById('f-status').value;
    const price    = document.getElementById('f-price').value;
    const duration = document.getElementById('f-duration').value;
    const featured = document.getElementById('f-featured').value;
    const allRows  = Array.from(document.querySelectorAll('#pkg-tbody tr.table-row'));

    visibleRows = allRows.filter(row => {
      const nm = row.dataset.name.includes(search);
      const cm = !category || row.dataset.category === category;
      const sm = !status   || row.dataset.status   === status;
      const fm = !featured || row.dataset.featured  === featured;
      return nm && cm && sm && fm;
    });

    if (price === 'asc')    visibleRows.sort((a,b) => +a.dataset.price - +b.dataset.price);
    if (price === 'desc')   visibleRows.sort((a,b) => +b.dataset.price - +a.dataset.price);
    if (duration === 'asc') visibleRows.sort((a,b) => +a.dataset.duration - +b.dataset.duration);
    if (duration === 'desc')visibleRows.sort((a,b) => +b.dataset.duration - +a.dataset.duration);

    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function resetFilters() {
    document.getElementById('pkg-search').value = '';
    ['f-category','f-status','f-price','f-duration','f-featured'].forEach(id => document.getElementById(id).value = '');
    applyFilters();
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} package${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    const mk = (html, disabled, onClick, extra='') => { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all ${extra}`; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage===1, () => { currentPage--; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
  }

  // -- Bulk Select -------------------------------------------------------
  function toggleAll(cb) { document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked); updateBulkBar(); }
  function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    const bar = document.getElementById('bulk-bar');
    if (checked > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); document.getElementById('bulk-count').textContent = `${checked} selected`; }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); document.getElementById('select-all').checked = false; }
  }
  function clearSelection() { document.querySelectorAll('.row-check, #select-all').forEach(c => c.checked = false); updateBulkBar(); }
  function bulkDelete() {
    const n = document.querySelectorAll('.row-check:checked').length;
    Swal.fire({ title: `Delete ${n} package${n>1?'s':''}?`, text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) { clearSelection(); Swal.fire({ title: 'Deleted!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } });
  }

  // -- Status Toggle -----------------------------------------------------
  function toggleStatus(id, el) {
    const newStatus = el.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${newStatus}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes' })
      .then(r => { if (!r.isConfirmed) el.checked = !el.checked; });
  }

  // -- View Drawer -------------------------------------------------------
  function openDrawer(pkg) {
    document.getElementById('d-image').src = pkg.image;
    document.getElementById('d-name').textContent = pkg.name;
    document.getElementById('d-category').textContent = pkg.category + ' Package';
    const fp = pkg.price - (pkg.price * pkg.discount / 100);
    document.getElementById('d-price').textContent = '₹' + fp.toLocaleString('en-IN');
    const op = document.getElementById('d-original-price');
    op.textContent = pkg.discount > 0 ? '₹' + Number(pkg.price).toLocaleString('en-IN') : '';
    const hrs = Math.floor(pkg.duration / 60), mins = pkg.duration % 60;
    document.getElementById('d-duration').textContent = (hrs > 0 ? hrs + 'h ' : '') + (mins > 0 ? mins + 'm' : '');
    document.getElementById('d-bookings').textContent = pkg.bookings;
    document.getElementById('d-discount').textContent = pkg.discount > 0 ? pkg.discount + '% OFF' : 'None';
    const sb = document.getElementById('d-status-badge');
    sb.textContent = pkg.status;
    sb.className = 'text-xs font-semibold px-3 py-1.5 rounded-full ' + (pkg.status === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500');
    const fb = document.getElementById('d-featured-badge');
    pkg.featured ? fb.classList.remove('hidden') : fb.classList.add('hidden');
    const svcs = document.getElementById('d-services');
    svcs.innerHTML = pkg.services.map(s => `<span class="text-xs font-medium bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full">${s}</span>`).join('');
    document.getElementById('d-description').textContent = pkg.description;
    document.getElementById('d-created').textContent = new Date(pkg.created).toLocaleDateString('en-IN', {day:'numeric',month:'long',year:'numeric'});
    document.getElementById('d-edit-btn').href = `edit.php?id=${pkg.id}`;
    document.getElementById('d-delete-btn').href = `delete.php?id=${pkg.id}`;
    document.getElementById('d-delete-btn').onclick = (e) => { if(!confirm('Delete this package?')) e.preventDefault(); };
    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }
  function closeDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }

  function deletePackage(id) {
    Swal.fire({ title: 'Delete Package?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Deleted!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } });
  }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#pkg-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
