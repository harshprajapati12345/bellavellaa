<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }

    /* Sidebar */
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-black-text, .sidebar-black-text span, .sidebar-black-text i,
    .sidebar-black-text a span, .sidebar-black-text button span { color: #000000 !important; }
    .sidebar-black-text [data-lucide] { color: #000000 !important; opacity: 0.8; transition: opacity 0.2s; }
    .sidebar-black-text a:hover [data-lucide], .sidebar-black-text button:hover [data-lucide] { opacity: 1; }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }

    /* Table */
    .table-row { transition: background 0.15s; }
    .table-row:hover { background: #fafafa; }
    .table-row.selected { background: #f0f9ff; }

    /* Status toggle */
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

    /* Modal */
    .modal-backdrop { transition: opacity 0.2s; }
    .modal-box { transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), opacity 0.2s; }
    .modal-backdrop.hidden .modal-box { transform: scale(0.95); opacity: 0; }

    /* Drawer */
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }

    /* Image upload preview */
    .img-upload-area { border: 2px dashed #e5e7eb; transition: border-color 0.2s, background 0.2s; }
    .img-upload-area:hover { border-color: #000; background: #fafafa; }

    /* Pagination */
    .page-btn { transition: all 0.15s; }
    .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }

    /* Category badge */
    .badge-luxe { background: #f5f3ff; color: #7c3aed; }
    .badge-prime { background: #fff7ed; color: #c2410c; }
    .badge-bridal { background: #fdf2f8; color: #be185d; }
    .badge-grooming { background: #f0fdf4; color: #15803d; }
    .badge-spa { background: #eff6ff; color: #1d4ed8; }
    .badge-skin { background: #fefce8; color: #a16207; }

    /* Stat card */
    .stat-card { transition: box-shadow 0.2s; }
    .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }

    /* Bulk bar */
    #bulk-bar { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
  </style>
</head>

<body class="antialiased selection:bg-gray-200">

<div class="flex min-h-screen relative">

  <?php include '../includes/sidebar.php'; ?>

  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">

    <?php $pageTitle = 'Services'; include '../includes/header.php'; ?>

    <?php
    // -- Mock Data --------------------------------------------------------------
    $services = [
      ['id'=>1,'name'=>'HD Bridal Makeup','category'=>'Bridal','duration'=>120,'price'=>8500,'status'=>'Active','featured'=>true,'bookings'=>142,'created'=>'2024-01-15','image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','desc'=>'Full HD bridal makeup with airbrush finish, long-lasting formula, and premium products.'],
      ['id'=>2,'name'=>'Aromatherapy Massage','category'=>'Spa','duration'=>60,'price'=>3200,'status'=>'Active','featured'=>false,'bookings'=>89,'created'=>'2024-01-20','image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=400&q=80','desc'=>'Relaxing full-body massage using essential oils to relieve stress and tension.'],
      ['id'=>3,'name'=>'Hydra Facial','category'=>'Skin','duration'=>75,'price'=>4500,'status'=>'Active','featured'=>true,'bookings'=>211,'created'=>'2024-02-01','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','desc'=>'Deep cleansing hydra facial that exfoliates, extracts, and hydrates skin.'],
      ['id'=>4,'name'=>'Classic Haircut','category'=>'Grooming','duration'=>45,'price'=>800,'status'=>'Active','featured'=>false,'bookings'=>320,'created'=>'2024-02-10','image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=400&q=80','desc'=>'Precision haircut styled to your preference with a professional finish.'],
      ['id'=>5,'name'=>'Party Glam Makeup','category'=>'Luxe','duration'=>90,'price'=>5500,'status'=>'Active','featured'=>true,'bookings'=>67,'created'=>'2024-02-14','image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=400&q=80','desc'=>'Glamorous party makeup with contouring, highlights, and long-lasting finish.'],
      ['id'=>6,'name'=>'Deep Tissue Massage','category'=>'Spa','duration'=>90,'price'=>4200,'status'=>'Inactive','featured'=>false,'bookings'=>18,'created'=>'2024-02-20','image'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=400&q=80','desc'=>'Therapeutic deep tissue massage targeting chronic muscle tension and pain.'],
      ['id'=>7,'name'=>'Nail Art Deluxe','category'=>'Prime','duration'=>60,'price'=>1800,'status'=>'Active','featured'=>false,'bookings'=>95,'created'=>'2024-03-01','image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80','desc'=>'Creative nail art with gel polish, designs, and nail extensions.'],
      ['id'=>8,'name'=>'Gold Facial','category'=>'Luxe','duration'=>60,'price'=>6000,'status'=>'Inactive','featured'=>true,'bookings'=>34,'created'=>'2024-03-05','image'=>'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&w=400&q=80','desc'=>'Luxurious 24K gold facial for anti-aging, brightening, and skin rejuvenation.'],
    ];

    $totalServices   = count($services);
    $activeServices  = count(array_filter($services, fn($s) => $s['status'] === 'Active'));
    $inactiveServices = $totalServices - $activeServices;
    $mostBooked = array_reduce($services, fn($carry, $s) => (!$carry || $s['bookings'] > $carry['bookings']) ? $s : $carry, null);
    ?>

    <div class="flex flex-col gap-6">

      <!-- -- Page Header --------------------------------------------------- -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Services</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all beauty services</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <!-- Search -->
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="svc-search" type="text" placeholder="Search services..." oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
          </div>
          <!-- Filter toggle (mobile) -->
          <button onclick="document.getElementById('filter-bar').classList.toggle('hidden')"
            class="sm:hidden flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
          </button>
          <!-- Add Service -->
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Service
          </a>
        </div>
      </div>

      <!-- -- Stat Cards ---------------------------------------------------- -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $totalServices; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Services</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="layers" class="w-5 h-5 text-gray-600"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $activeServices; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Live now</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Inactive</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $inactiveServices; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Paused</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="pause-circle" class="w-5 h-5 text-gray-400"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Top Booked</p>
            <p class="text-base font-bold text-gray-900 leading-tight"><?php echo $mostBooked['name']; ?></p>
            <p class="text-xs text-gray-400 mt-0.5"><?php echo $mostBooked['bookings']; ?> bookings</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-amber-500"></i>
          </div>
        </div>

      </div>

      <!-- -- Filter Bar ---------------------------------------------------- -->
      <div id="filter-bar" class="bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest mr-1">Filters</span>

        <select id="f-category" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">All Categories</option>
          <option value="Luxe">Luxe</option>
          <option value="Prime">Prime</option>
          <option value="Bridal">Bridal</option>
          <option value="Grooming">Grooming</option>
          <option value="Spa">Spa</option>
          <option value="Skin">Skin</option>
        </select>

        <select id="f-status" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <select id="f-price" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">Price Sort</option>
          <option value="asc">Price: Low ? High</option>
          <option value="desc">Price: High ? Low</option>
        </select>

        <select id="f-duration" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">Duration Sort</option>
          <option value="asc">Duration: Short ? Long</option>
          <option value="desc">Duration: Long ? Short</option>
        </select>

        <button onclick="resetFilters()"
          class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-black px-3 py-2 rounded-xl hover:bg-gray-100 transition-all ml-auto">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
        </button>
      </div>

      <!-- -- Bulk Action Bar (hidden by default) --------------------------- -->
      <div id="bulk-bar" class="hidden bg-black text-white rounded-2xl px-5 py-3 flex items-center gap-4">
        <span id="bulk-count" class="text-sm font-medium"></span>
        <div class="flex-1"></div>
        <button onclick="bulkDelete()" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-all">
          <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Selected
        </button>
        <button onclick="clearSelection()" class="text-gray-400 hover:text-white transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- -- Services Table ------------------------------------------------ -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left w-10">
                  <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                    class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer">
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Duration</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Created</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="services-tbody">

              <?php foreach($services as $svc):
                $catClass = 'badge-' . strtolower($svc['category']);
              ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $svc['id']; ?>"
                  data-name="<?php echo strtolower($svc['name']); ?>"
                  data-category="<?php echo $svc['category']; ?>"
                  data-status="<?php echo $svc['status']; ?>"
                  data-price="<?php echo $svc['price']; ?>"
                  data-duration="<?php echo $svc['duration']; ?>">

                <td class="px-5 py-4">
                  <input type="checkbox" class="row-check w-4 h-4 rounded border-gray-300 accent-black cursor-pointer" onchange="onRowCheck()">
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                      <img src="<?php echo $svc['image']; ?>" alt="<?php echo $svc['name']; ?>" class="w-full h-full object-cover">
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 leading-tight"><?php echo $svc['name']; ?></p>
                      <?php if($svc['featured']): ?>
                      <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full mt-0.5">
                        <i data-lucide="star" class="w-2.5 h-2.5 fill-current"></i> Featured
                      </span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $catClass; ?>">
                    <?php echo $svc['category']; ?>
                  </span>
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5 text-sm text-gray-600">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                    <?php echo $svc['duration']; ?> mins
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span class="text-sm font-semibold text-gray-900">?<?php echo number_format($svc['price']); ?></span>
                </td>

                <td class="px-5 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" <?php echo $svc['status'] === 'Active' ? 'checked' : ''; ?>
                      onchange="toggleStatus(<?php echo $svc['id']; ?>, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>

                <td class="px-5 py-4">
                  <span class="text-sm text-gray-500"><?php echo date('d M Y', strtotime($svc['created'])); ?></span>
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="view.php?id=<?php echo $svc['id']; ?>" title="View"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="edit.php?id=<?php echo $svc['id']; ?>" title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="delete.php?id=<?php echo $svc['id']; ?>" onclick="return confirm('Delete this service?')" title="Delete"
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
            <i data-lucide="search-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No services found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters</p>
          <button onclick="resetFilters()" class="mt-4 px-5 py-2 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all">
            Reset Filters
          </button>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>

      </div>

    </div><!-- /flex-col gap-6 -->
  </main>
</div>



<!-- -----------------------------------------------------------------------
     VIEW SERVICE DRAWER
----------------------------------------------------------------------- -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">

  <!-- Drawer Header -->
  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <h3 class="text-lg font-semibold text-gray-900">Service Details</h3>
    <button onclick="closeDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>

  <!-- Drawer Body -->
  <div class="flex-1 overflow-y-auto">
    <!-- Image -->
    <div class="h-56 bg-gray-100 relative">
      <img id="d-image" src="" alt="" class="w-full h-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
      <div id="d-status-badge" class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md"></div>
    </div>

    <!-- Details -->
    <div class="p-6 flex flex-col gap-5">
      <div>
        <h2 id="d-name" class="text-2xl font-bold text-gray-900 leading-tight"></h2>
        <div id="d-featured-badge" class="hidden inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full mt-2">
          <i data-lucide="star" class="w-3 h-3 fill-current"></i> Featured Service
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <i data-lucide="tag" class="w-4 h-4 text-gray-400 mx-auto mb-1"></i>
          <p id="d-price" class="text-lg font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Price</p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <i data-lucide="clock" class="w-4 h-4 text-gray-400 mx-auto mb-1"></i>
          <p id="d-duration" class="text-lg font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Duration</p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <i data-lucide="calendar-check" class="w-4 h-4 text-gray-400 mx-auto mb-1"></i>
          <p id="d-bookings" class="text-lg font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Bookings</p>
        </div>
      </div>

      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Category</p>
        <span id="d-category" class="text-sm font-semibold px-3 py-1.5 rounded-full"></span>
      </div>

      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Description</p>
        <p id="d-desc" class="text-sm text-gray-600 leading-relaxed"></p>
      </div>

      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Created</p>
        <p id="d-created" class="text-sm text-gray-600"></p>
      </div>
    </div>
  </div>

  <!-- Drawer Footer -->
  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <a id="d-edit-btn" href="#"
      class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
      <i data-lucide="pencil" class="w-4 h-4"></i> Edit Service
    </a>
    <a id="d-delete-btn" href="#"
      class="w-12 h-12 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
      <i data-lucide="trash-2" class="w-4 h-4"></i>
    </a>
  </div>

</div>

<!-- -----------------------------------------------------------------------
     SCRIPTS
----------------------------------------------------------------------- -->
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  // -- Sidebar toggles ------------------------------------------------------
  // Sidebar toggles are handled by sidebar.php

  // -- Pagination state -----------------------------------------------------
  const ROWS_PER_PAGE = 5;
  let currentPage = 1;
  let visibleRows = [];

  // -- Filter & Search ------------------------------------------------------
  function applyFilters() {
    const search   = document.getElementById('svc-search').value.toLowerCase();
    const category = document.getElementById('f-category').value;
    const status   = document.getElementById('f-status').value;
    const priceSort = document.getElementById('f-price').value;
    const durSort  = document.getElementById('f-duration').value;

    const allRows = Array.from(document.querySelectorAll('#services-tbody tr.table-row'));

    // Filter
    visibleRows = allRows.filter(row => {
      const nameMatch = row.dataset.name.includes(search);
      const catMatch  = !category || row.dataset.category === category;
      const statMatch = !status   || row.dataset.status === status;
      return nameMatch && catMatch && statMatch;
    });

    // Sort
    if (priceSort) {
      visibleRows.sort((a, b) => priceSort === 'asc'
        ? a.dataset.price - b.dataset.price
        : b.dataset.price - a.dataset.price);
    } else if (durSort) {
      visibleRows.sort((a, b) => durSort === 'asc'
        ? a.dataset.duration - b.dataset.duration
        : b.dataset.duration - a.dataset.duration);
    }

    // Hide all, then show filtered in sorted order
    allRows.forEach(r => { r.style.display = 'none'; r.style.order = ''; });
    visibleRows.forEach((r, i) => { r.style.order = i; });

    currentPage = 1;
    renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end   = start + ROWS_PER_PAGE;

    visibleRows.forEach((r, i) => {
      r.style.display = (i >= start && i < end) ? '' : 'none';
    });

    // Empty state
    const empty = document.getElementById('empty-state');
    const paginationWrap = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) {
      empty.classList.remove('hidden');
      empty.classList.add('flex');
      paginationWrap.classList.add('hidden');
    } else {
      empty.classList.add('hidden');
      empty.classList.remove('flex');
      paginationWrap.classList.remove('hidden');
    }

    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length;
    const totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total);
    const end   = Math.min(currentPage * ROWS_PER_PAGE, total);

    document.getElementById('pagination-info').textContent =
      `Showing ${start}–${end} of ${total} service${total !== 1 ? 's' : ''}`;

    const btns = document.getElementById('pagination-btns');
    btns.innerHTML = '';

    // Prev
    const prev = document.createElement('button');
    prev.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
    prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>';
    prev.disabled = currentPage === 1;
    prev.onclick = () => { currentPage--; renderPage(); };
    btns.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement('button');
      btn.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i === currentPage ? 'active border-black' : 'border-gray-200 text-gray-600'}`;
      btn.textContent = i;
      btn.onclick = () => { currentPage = i; renderPage(); };
      btns.appendChild(btn);
    }

    // Next
    const next = document.createElement('button');
    next.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
    next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
    next.disabled = currentPage === totalPages;
    next.onclick = () => { currentPage++; renderPage(); };
    btns.appendChild(next);
  }

  function resetFilters() {
    document.getElementById('svc-search').value = '';
    document.getElementById('f-category').value = '';
    document.getElementById('f-status').value = '';
    document.getElementById('f-price').value = '';
    document.getElementById('f-duration').value = '';
    applyFilters();
  }

  // -- Bulk Selection -------------------------------------------------------
  function toggleSelectAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    onRowCheck();
  }

  function onRowCheck() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulk-bar');
    if (checked.length > 0) {
      bar.classList.remove('hidden');
      bar.classList.add('flex');
      document.getElementById('bulk-count').textContent = `${checked.length} service${checked.length > 1 ? 's' : ''} selected`;
    } else {
      bar.classList.add('hidden');
      bar.classList.remove('flex');
    }
    // Sync select-all checkbox
    const all = document.querySelectorAll('.row-check');
    document.getElementById('select-all').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('select-all').checked = checked.length === all.length;
  }

  function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    onRowCheck();
  }

  function bulkDelete() {
    const count = document.querySelectorAll('.row-check:checked').length;
    Swal.fire({
      title: `Delete ${count} service${count > 1 ? 's' : ''}?`,
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete',
    }).then(r => {
      if (r.isConfirmed) {
        Swal.fire({ title: 'Deleted!', text: `${count} service${count > 1 ? 's' : ''} removed.`, icon: 'success', confirmButtonColor: '#000', timer: 2000, showConfirmButton: false });
        clearSelection();
      }
    });
  }

  // -- Toggle Status --------------------------------------------------------
  function toggleStatus(id, checkbox) {
    const newStatus = checkbox.checked ? 'Active' : 'Inactive';
    Swal.fire({
      title: `Set to ${newStatus}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#000',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: `Yes, ${newStatus}`,
    }).then(r => {
      if (!r.isConfirmed) checkbox.checked = !checkbox.checked; // revert
      else Swal.fire({ title: 'Updated!', text: `Service is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
    });
  }

  // -- Delete ---------------------------------------------------------------
  function deleteService(id) {
    Swal.fire({
      title: 'Delete Service?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete',
    }).then(r => {
      if (r.isConfirmed) {
        Swal.fire({ title: 'Deleted!', text: 'Service has been removed.', icon: 'success', confirmButtonColor: '#000', timer: 2000, showConfirmButton: false });
      }
    });
  }

  // -- Modal ----------------------------------------------------------------
  let editingId = null;

  function openModal(svc) {
    editingId = svc ? svc.id : null;
    document.getElementById('modal-title').textContent = svc ? 'Edit Service' : 'Add Service';
    document.getElementById('modal-save-label').textContent = svc ? 'Update Service' : 'Save Service';

    document.getElementById('f-name').value    = svc ? svc.name : '';
    document.getElementById('f-cat').value     = svc ? svc.category : '';
    document.getElementById('f-price').value   = svc ? svc.price : '';
    document.getElementById('f-duration').value = svc ? svc.duration : '';
    document.getElementById('f-desc').value    = svc ? svc.desc : '';
    document.getElementById('f-status').checked   = svc ? svc.status === 'Active' : true;
    document.getElementById('f-featured').checked = svc ? svc.featured : false;
    document.getElementById('f-status-label').textContent = (svc ? svc.status === 'Active' : true) ? 'Active' : 'Inactive';

    // Image preview
    const previewWrap = document.getElementById('img-preview-wrap');
    const placeholder = document.getElementById('img-placeholder');
    if (svc && svc.image) {
      document.getElementById('img-preview').src = svc.image;
      previewWrap.classList.remove('hidden');
      placeholder.classList.add('hidden');
    } else {
      previewWrap.classList.add('hidden');
      placeholder.classList.remove('hidden');
    }

    const modal = document.getElementById('svc-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeModal() {
    const modal = document.getElementById('svc-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
  }

  document.getElementById('f-status').addEventListener('change', function() {
    document.getElementById('f-status-label').textContent = this.checked ? 'Active' : 'Inactive';
  });

  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('img-preview').src = e.target.result;
        document.getElementById('img-preview-wrap').classList.remove('hidden');
        document.getElementById('img-placeholder').classList.add('hidden');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function saveService() {
    const name = document.getElementById('f-name').value.trim();
    const cat  = document.getElementById('f-cat').value;
    const price = document.getElementById('f-price').value;
    const dur  = document.getElementById('f-duration').value;

    if (!name || !cat || !price || !dur) {
      Swal.fire({ title: 'Missing Fields', text: 'Please fill in all required fields.', icon: 'warning', confirmButtonColor: '#000' });
      return;
    }

    closeModal();
    Swal.fire({
      title: editingId ? 'Service Updated!' : 'Service Added!',
      text: editingId ? `"${name}" has been updated.` : `"${name}" has been added.`,
      icon: 'success',
      confirmButtonColor: '#000',
      timer: 2000,
      showConfirmButton: false
    });
  }

  // Close modal on backdrop click
  document.getElementById('svc-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  // -- Drawer ---------------------------------------------------------------
  const catClasses = {
    'Luxe': 'badge-luxe', 'Prime': 'badge-prime', 'Bridal': 'badge-bridal',
    'Grooming': 'badge-grooming', 'Spa': 'badge-spa', 'Skin': 'badge-skin'
  };

  function openDrawer(svc) {
    document.getElementById('d-image').src = svc.image;
    document.getElementById('d-name').textContent = svc.name;
    document.getElementById('d-price').textContent = '?' + Number(svc.price).toLocaleString('en-IN');
    document.getElementById('d-duration').textContent = svc.duration + ' m';
    document.getElementById('d-bookings').textContent = svc.bookings;
    document.getElementById('d-desc').textContent = svc.desc;

    // Created date
    const d = new Date(svc.created);
    document.getElementById('d-created').textContent = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });

    // Status badge
    const sb = document.getElementById('d-status-badge');
    sb.textContent = svc.status;
    sb.className = 'absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md ' +
      (svc.status === 'Active' ? 'bg-white/90 text-emerald-600' : 'bg-gray-200/90 text-gray-500');

    // Category
    const dc = document.getElementById('d-category');
    dc.textContent = svc.category;
    dc.className = 'text-sm font-semibold px-3 py-1.5 rounded-full ' + (catClasses[svc.category] || '');

    // Featured
    const fb = document.getElementById('d-featured-badge');
    svc.featured ? fb.classList.remove('hidden') : fb.classList.add('hidden');

    // Footer buttons
    document.getElementById('d-edit-btn').onclick = () => { closeDrawer(); openModal(svc); };
    document.getElementById('d-delete-btn').onclick = () => deleteService(svc.id);

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    const panel = document.getElementById('drawer-panel');
    panel.classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeDrawer() {
    document.getElementById('drawer-panel').classList.add('closed');
    document.getElementById('drawer-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
  }

  // -- Init -----------------------------------------------------------------
  (function init() {
    visibleRows = Array.from(document.querySelectorAll('#services-tbody tr.table-row'));
    renderPage();
  })();
</script>

</body>
</html>
