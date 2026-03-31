<?php
$pageTitle = 'Categories';

/* -- All Categories ------------------------------------------------------- */
$categories = [
    ['id'=>10,'name'=>'Bridal',       'slug'=>'bridal',       'services'=>14,'bookings'=>342,'status'=>'Active',  'featured'=>true, 'created'=>'2024-01-10','color'=>'#be185d','image'=>'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80','desc'=>'Comprehensive bridal beauty packages for the most important day.'],
    ['id'=>11,'name'=>'Spa & Wellness','slug'=>'spa-wellness', 'services'=>8, 'bookings'=>189,'status'=>'Active',  'featured'=>true, 'created'=>'2024-01-15','color'=>'#1d4ed8','image'=>'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=400&q=80','desc'=>'Relaxing spa treatments and wellness rituals for mind and body.'],
    ['id'=>12,'name'=>'Makeup',        'slug'=>'makeup',       'services'=>9, 'bookings'=>267,'status'=>'Active',  'featured'=>true, 'created'=>'2024-02-10','color'=>'#7c3aed','image'=>'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=400&q=80','desc'=>'Expert makeup artistry for parties, events, and everyday glam.'],
    ['id'=>13,'name'=>'Facial',        'slug'=>'facial',       'services'=>6, 'bookings'=>210,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-05','color'=>'#db2777','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','desc'=>'Luxury facial treatments using premium skincare products.'],
    ['id'=>20,'name'=>'Hair Care',     'slug'=>'hair-care',    'services'=>7, 'bookings'=>155,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-01','color'=>'#0891b2','image'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80','desc'=>'Complete hair care from cuts and styling to treatments.'],
    ['id'=>21,'name'=>'Hair Color',    'slug'=>'hair-color',   'services'=>5, 'bookings'=>132,'status'=>'Active',  'featured'=>false,'created'=>'2024-03-10','color'=>'#d97706','image'=>'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=400&q=80','desc'=>'Professional hair colouring, highlights, and balayage.'],
    ['id'=>22,'name'=>'Nail Art',      'slug'=>'nail-art',     'services'=>5, 'bookings'=>98, 'status'=>'Inactive','featured'=>false,'created'=>'2024-02-20','color'=>'#e11d48','image'=>'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80','desc'=>'Creative nail art, gel extensions, and nail care treatments.'],
    ['id'=>23,'name'=>'Grooming',      'slug'=>'grooming',     'services'=>6, 'bookings'=>520,'status'=>'Active',  'featured'=>false,'created'=>'2024-02-01','color'=>'#15803d','image'=>'https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=400&q=80','desc'=>'Professional grooming services for a sharp, polished look.'],
    ['id'=>24,'name'=>'Skin Care',     'slug'=>'skin-care',    'services'=>11,'bookings'=>411,'status'=>'Active',  'featured'=>false,'created'=>'2024-01-20','color'=>'#a16207','image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=400&q=80','desc'=>'Advanced skin care treatments using the latest technology.'],
];

$totalCats     = count($categories);
$totalSvcs     = array_sum(array_column($categories, 'services'));
$totalBookings = array_sum(array_column($categories, 'bookings'));
$totalActive   = count(array_filter($categories, fn($s) => $s['status'] === 'Active'));

$topCategory = array_reduce($categories, function($carry, $item) {
    return ($item['bookings'] > ($carry['bookings'] ?? 0)) ? $item : $carry;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories � Bellavella Admin</title>
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
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Categories'; include '../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- -- Page Header ---------------------------------------------------- -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Service Categories</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all service categories in one place</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
          </a>
        </div>
      </div>

      <!-- -- Stat Cards ----------------------------------------------------- -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalCats; ?></p><p class="text-xs text-gray-400 mt-0.5">Categories</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="folder" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalActive; ?></p><p class="text-xs text-gray-400 mt-0.5">Live categories</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Services</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalSvcs; ?></p><p class="text-xs text-gray-400 mt-0.5">Active services</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="store" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Top Performing</p><p class="text-lg font-bold text-gray-900 mt-1"><?php echo $topCategory['name']; ?></p><p class="text-xs text-gray-400 mt-0.5"><?php echo number_format($topCategory['bookings']); ?> bookings</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-violet-500"></i></div>
        </div>
      </div>

      <!-- -- Table Layout ----------------------------------------------- -->
      <div class="flex flex-col gap-4">

        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="relative">
              <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
              <input id="cat-search" type="text" placeholder="Search categories�" oninput="applyFilters()"
                class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
            </div>
            <select id="f-status" onchange="applyFilters()"
              class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer transition-all">
              <option value="">All Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Services</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                  <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
              </thead>
              <tbody id="categories-tbody">
                <?php foreach($categories as $cat): ?>
                <tr class="table-row border-b border-gray-50"
                    data-name="<?php echo strtolower($cat['name']); ?>"
                    data-status="<?php echo $cat['status']; ?>">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="<?php echo $cat['image']; ?>" class="w-full h-full object-cover" alt="">
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900"><?php echo $cat['name']; ?></p>
                        <p class="text-[11px] text-gray-400 mt-0.5">/<?php echo $cat['slug']; ?></p>
                      </div>
                    </div>
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
            <p class="text-gray-500 font-medium">No categories found</p>
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
  </main>
</div>

<!-- -- VIEW DRAWER ----------------------------------------------------------- -->
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

<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  // -- Pagination & Filters ---------------------------------------------------
  const ROWS_PER_PAGE = 8;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search = document.getElementById('cat-search').value.toLowerCase();
    const status = document.getElementById('f-status').value;
    const allRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row'));
    
    visibleRows = allRows.filter(row => {
      const nameMatch   = row.dataset.name.includes(search);
      const statusMatch = !status || row.dataset.status === status;
      return nameMatch && statusMatch;
    });
    
    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE, end = start + ROWS_PER_PAGE;
    visibleRows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
    
    const empty = document.getElementById('empty-state'), pw = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) {
      empty.classList.remove('hidden'); empty.classList.add('flex');
      pw.classList.add('hidden');
    } else {
      empty.classList.add('hidden'); empty.classList.remove('flex');
      pw.classList.remove('hidden');
    }
    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length, totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total), end = Math.min(currentPage * ROWS_PER_PAGE, total);
    
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} categor${total !== 1 ? 'ies' : 'y'}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    
    const mkArrow = (svg, disabled, onClick) => {
        const b = document.createElement('button');
        b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
        b.innerHTML = svg; b.disabled = disabled; b.onclick = onClick;
        return b;
    };
    
    btns.appendChild(mkArrow('<i data-lucide="chevron-left" class="w-4 h-4"></i>', currentPage===1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) {
        const b = document.createElement('button');
        b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`;
        b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); };
        btns.appendChild(b);
    }
    btns.appendChild(mkArrow('<i data-lucide="chevron-right" class="w-4 h-4"></i>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }));
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  // -- Toggle Status ---------------------------------------------------------
  function toggleStatus(id, checkbox) {
    const newStatus = checkbox.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${newStatus}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, ${newStatus}` })
      .then(r => {
        if (!r.isConfirmed) checkbox.checked = !checkbox.checked;
        else Swal.fire({ title: 'Updated!', text: `Category is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
      });
  }

  // -- Drawer ----------------------------------------------------------------
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

  // -- Init ------------------------------------------------------------------
  (function init() {
    visibleRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row'));
    renderPage();
  })();
</script>
</body>
</html>
