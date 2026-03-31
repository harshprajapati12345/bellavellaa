<?php
$pageTitle = 'Users';

/* -- Sample Users Data ----------------------------------------------------- */
$users = [
    ['id'=>1, 'name'=>'Ananya Kapoor',   'email'=>'ananya@example.com',   'phone'=>'+91 98765 43210', 'city'=>'Mumbai',    'status'=>'Active',   'bookings'=>14, 'joined'=>'2024-01-15', 'avatar'=>'https://i.pravatar.cc/80?img=1'],
    ['id'=>2, 'name'=>'Priya Sharma',    'email'=>'priya@example.com',    'phone'=>'+91 91234 56789', 'city'=>'Delhi',     'status'=>'Active',   'bookings'=>9,  'joined'=>'2024-02-03', 'avatar'=>'https://i.pravatar.cc/80?img=5'],
    ['id'=>3, 'name'=>'Meera Patel',     'email'=>'meera@example.com',    'phone'=>'+91 99887 76655', 'city'=>'Ahmedabad', 'status'=>'Active',   'bookings'=>22, 'joined'=>'2024-01-28', 'avatar'=>'https://i.pravatar.cc/80?img=9'],
    ['id'=>4, 'name'=>'Sneha Gupta',     'email'=>'sneha@example.com',    'phone'=>'+91 87654 32100', 'city'=>'Pune',      'status'=>'Inactive', 'bookings'=>3,  'joined'=>'2024-03-10', 'avatar'=>'https://i.pravatar.cc/80?img=10'],
    ['id'=>5, 'name'=>'Kavya Reddy',     'email'=>'kavya@example.com',    'phone'=>'+91 95555 11223', 'city'=>'Hyderabad', 'status'=>'Active',   'bookings'=>18, 'joined'=>'2024-02-20', 'avatar'=>'https://i.pravatar.cc/80?img=21'],
    ['id'=>6, 'name'=>'Divya Nair',      'email'=>'divya@example.com',    'phone'=>'+91 80000 12345', 'city'=>'Bangalore', 'status'=>'Active',   'bookings'=>7,  'joined'=>'2024-03-05', 'avatar'=>'https://i.pravatar.cc/80?img=25'],
    ['id'=>7, 'name'=>'Riya Mehta',      'email'=>'riya@example.com',     'phone'=>'+91 70001 23456', 'city'=>'Jaipur',    'status'=>'Inactive', 'bookings'=>1,  'joined'=>'2024-04-01', 'avatar'=>'https://i.pravatar.cc/80?img=30'],
    ['id'=>8, 'name'=>'Neha Joshi',      'email'=>'neha@example.com',     'phone'=>'+91 96666 54321', 'city'=>'Chennai',   'status'=>'Active',   'bookings'=>11, 'joined'=>'2024-01-10', 'avatar'=>'https://i.pravatar.cc/80?img=35'],
    ['id'=>9, 'name'=>'Aisha Khan',      'email'=>'aisha@example.com',    'phone'=>'+91 93333 22111', 'city'=>'Lucknow',   'status'=>'Active',   'bookings'=>5,  'joined'=>'2024-03-22', 'avatar'=>'https://i.pravatar.cc/80?img=40'],
    ['id'=>10,'name'=>'Tanvi Singh',     'email'=>'tanvi@example.com',    'phone'=>'+91 94444 33222', 'city'=>'Kolkata',   'status'=>'Active',   'bookings'=>16, 'joined'=>'2024-02-14', 'avatar'=>'https://i.pravatar.cc/80?img=44'],
];

$totalUsers   = count($users);
$totalActive  = count(array_filter($users, fn($u) => $u['status'] === 'Active'));
$totalInactive= $totalUsers - $totalActive;
$totalBookings= array_sum(array_column($users, 'bookings'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users · Bellavella Admin</title>
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
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Users'; include '../includes/header.php'; ?>

    <div class="flex flex-col gap-6">

      <!-- -- Page Header ---------------------------------------------------- -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Users</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all customer accounts in one place</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Add User
          </a>
        </div>
      </div>

      <!-- -- Stat Cards ----------------------------------------------------- -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalUsers; ?></p><p class="text-xs text-gray-400 mt-0.5">Registered users</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalActive; ?></p><p class="text-xs text-gray-400 mt-0.5">Active accounts</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Inactive</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalInactive; ?></p><p class="text-xs text-gray-400 mt-0.5">Inactive accounts</p></div>
          <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0"><i data-lucide="user-x" class="w-5 h-5 text-red-400"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Bookings</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalBookings; ?></p><p class="text-xs text-gray-400 mt-0.5">Total bookings made</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="calendar-check" class="w-5 h-5 text-violet-500"></i></div>
        </div>
      </div>

      <!-- -- Table Layout ----------------------------------------------- -->
      <div class="flex flex-col gap-4">

        <!-- Toolbar -->
        <div class="flex flex-col gap-4">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="relative">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input id="user-search" type="text" placeholder="Search users..." oninput="applyFilters()"
                  class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
              </div>
            </div>
          </div>

          <!-- Filter Tabs -->
          <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-1.5">
            <?php foreach(['all'=>'All','Active'=>'Active','Inactive'=>'Inactive'] as $k=>$v): ?>
            <button onclick="setTab('<?php echo $k; ?>')" id="tab-<?php echo $k; ?>"
              class="filter-tab text-sm font-medium px-4 py-2 <?php echo $k==='all'?'active':''; ?>">
              <?php echo $v; ?>
            </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">User</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">City</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                  <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
              </thead>
              <tbody id="users-tbody">
                <?php foreach($users as $user): ?>
                <tr class="table-row border-b border-gray-50"
                    data-name="<?php echo strtolower($user['name'].' '.$user['email']); ?>"
                    data-status="<?php echo $user['status']; ?>">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <img src="<?php echo $user['avatar']; ?>" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100" alt="">
                      <div>
                        <p class="text-sm font-semibold text-gray-900"><?php echo $user['name']; ?></p>
                        <p class="text-[11px] text-gray-400 mt-0.5"><?php echo $user['email']; ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600"><?php echo $user['phone']; ?></span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600"><?php echo $user['city']; ?></span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-900">
                      <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                      <?php echo $user['bookings']; ?>
                    </span>
                  </td>
                  <td class="px-5 py-4">
                    <label class="toggle-switch">
                      <input type="checkbox" <?php echo $user['status']==='Active'?'checked':''; ?> onchange="toggleStatus(<?php echo $user['id']; ?>, this)">
                      <span class="toggle-slider"></span>
                    </label>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                      <button onclick='openDrawer(<?php echo json_encode($user); ?>)' title="View"
                        class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      </button>
                      <a href="edit.php?id=<?php echo $user['id']; ?>" title="Edit"
                        class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                      </a>
                      <a href="delete.php?id=<?php echo $user['id']; ?>" title="Delete"
                        onclick="return confirm('Delete <?php echo addslashes($user['name']); ?>? This cannot be undone.')"
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
              <i data-lucide="user-x" class="w-7 h-7 text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium">No users found</p>
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

  <!-- Avatar header -->
  <div class="relative h-36 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
    <img id="d-avatar" src="" class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-lg" alt="">
    <button onclick="closeDrawer()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/80 backdrop-blur-md hover:bg-white flex items-center justify-center transition-all shadow">
      <i data-lucide="x" class="w-4 h-4 text-gray-700"></i>
    </button>
    <span id="d-status-badge" class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold"></span>
  </div>

  <!-- Body -->
  <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-5">
    <div class="text-center -mt-2">
      <h3 id="d-name" class="text-xl font-bold text-gray-900"></h3>
      <p id="d-email" class="text-sm text-gray-400 mt-0.5"></p>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-gray-50 rounded-2xl p-4 text-center">
        <p id="d-bookings" class="text-2xl font-bold text-gray-900"></p>
        <p class="text-xs text-gray-400 mt-0.5">Bookings</p>
      </div>
      <div class="bg-gray-50 rounded-2xl p-4 text-center">
        <p id="d-city" class="text-lg font-bold text-gray-900"></p>
        <p class="text-xs text-gray-400 mt-0.5">City</p>
      </div>
    </div>

    <!-- Details -->
    <div class="space-y-3">
      <div class="flex items-center gap-3 py-3 px-4 bg-gray-50 rounded-xl">
        <i data-lucide="phone" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Phone</p>
          <p id="d-phone" class="text-sm text-gray-700 font-medium"></p>
        </div>
      </div>
      <div class="flex items-center gap-3 py-3 px-4 bg-gray-50 rounded-xl">
        <i data-lucide="calendar" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Joined</p>
          <p id="d-joined" class="text-sm text-gray-700 font-medium"></p>
        </div>
      </div>
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
  let currentPage = 1, visibleRows = [], currentTab = 'all';

  function setTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    applyFilters();
  }

  function applyFilters() {
    const search = document.getElementById('user-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#users-tbody tr.table-row'));
    
    visibleRows = allRows.filter(row => {
      const nameMatch   = row.dataset.name.includes(search);
      const statusMatch = currentTab === 'all' || row.dataset.status === currentTab;
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
    
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} user${total !== 1 ? 's' : ''}`;
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
        else Swal.fire({ title: 'Updated!', text: `User is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
      });
  }

  // -- Drawer ----------------------------------------------------------------
  function openDrawer(user) {
    document.getElementById('d-avatar').src  = user.avatar;
    document.getElementById('d-name').textContent  = user.name;
    document.getElementById('d-email').textContent = user.email;
    document.getElementById('d-phone').textContent = user.phone;
    document.getElementById('d-city').textContent  = user.city;
    document.getElementById('d-bookings').textContent = user.bookings;

    const d = new Date(user.joined);
    document.getElementById('d-joined').textContent = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });

    const sb = document.getElementById('d-status-badge');
    sb.textContent = user.status;
    sb.className = 'absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold ' +
      (user.status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-500');

    document.getElementById('d-edit-btn').href = `edit.php?id=${user.id}`;
    document.getElementById('d-delete-btn').href = `delete.php?id=${user.id}`;
    document.getElementById('d-delete-btn').onclick = (e) => {
      e.preventDefault();
      Swal.fire({ title: 'Delete User?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
        .then(r => { if (r.isConfirmed) window.location.href = `delete.php?id=${user.id}`; });
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
    visibleRows = Array.from(document.querySelectorAll('#users-tbody tr.table-row'));
    renderPage();
  })();
</script>
</body>
</html>
