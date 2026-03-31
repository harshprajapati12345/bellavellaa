<?php
$pageTitle = 'Assign';

/* -- Mock bookings data --------------------------------------------------- */
$bookings = [
  ['id'=>1001,'customer'=>'Riya Kapoor','avatar'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98001 11001','service'=>'HD Bridal Makeup','package'=>'Luxe Bridal','date'=>'2026-02-20','slot'=>'10:00 AM','city'=>'Mumbai','status'=>'Unassigned','professional'=>null,'pro_id'=>null],
  ['id'=>1002,'customer'=>'Sneha Verma','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98002 22002','service'=>'Hydra Facial','package'=>'Prime Glow','date'=>'2026-02-20','slot'=>'12:00 PM','city'=>'Delhi','status'=>'Assigned','professional'=>'Sunita Rao','pro_id'=>3],
  ['id'=>1003,'customer'=>'Pooja Mehta','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98003 33003','service'=>'Nail Art Deluxe','package'=>'Prime Nails','date'=>'2026-02-21','slot'=>'02:00 PM','city'=>'Bangalore','status'=>'In Progress','professional'=>'Deepa Nair','pro_id'=>6],
  ['id'=>1004,'customer'=>'Ananya Singh','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98004 44004','service'=>'Aromatherapy Massage','package'=>'Luxe Relax','date'=>'2026-02-21','slot'=>'04:00 PM','city'=>'Pune','status'=>'Completed','professional'=>'Sunita Rao','pro_id'=>3],
  ['id'=>1005,'customer'=>'Divya Sharma','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98005 55005','service'=>'Party Glam','package'=>'Luxe Party','date'=>'2026-02-22','slot'=>'11:00 AM','city'=>'Chennai','status'=>'Unassigned','professional'=>null,'pro_id'=>null],
  ['id'=>1006,'customer'=>'Kavya Nair','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98006 66006','service'=>'Classic Haircut','package'=>'Prime Hair','date'=>'2026-02-22','slot'=>'03:00 PM','city'=>'Hyderabad','status'=>'Assigned','professional'=>'Anjali Mehta','pro_id'=>2],
  ['id'=>1007,'customer'=>'Meena Pillai','avatar'=>'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=facearea&facepad=2&w=64&h=64&q=80','phone'=>'+91 98007 77007','service'=>'Gold Facial','package'=>'Luxe Glow','date'=>'2026-02-23','slot'=>'09:00 AM','city'=>'Mumbai','status'=>'Unassigned','professional'=>null,'pro_id'=>null],
];

$professionals = [
  ['id'=>1,'name'=>'Priya Sharma','category'=>'Luxe','city'=>'Mumbai','rating'=>4.9,'available'=>true],
  ['id'=>2,'name'=>'Anjali Mehta','category'=>'Prime','city'=>'Delhi','rating'=>4.6,'available'=>true],
  ['id'=>3,'name'=>'Sunita Rao','category'=>'Luxe','city'=>'Bangalore','rating'=>4.8,'available'=>false],
  ['id'=>4,'name'=>'Kavita Joshi','category'=>'Prime','city'=>'Pune','rating'=>3.8,'available'=>true],
  ['id'=>6,'name'=>'Deepa Nair','category'=>'Prime','city'=>'Hyderabad','rating'=>4.7,'available'=>true],
];

$total      = count($bookings);
$unassigned = count(array_filter($bookings, fn($b) => $b['status'] === 'Unassigned'));
$assigned   = count(array_filter($bookings, fn($b) => $b['status'] === 'Assigned'));
$inProgress = count(array_filter($bookings, fn($b) => $b['status'] === 'In Progress'));
$completed  = count(array_filter($bookings, fn($b) => $b['status'] === 'Completed'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign · Bellavella Admin</title>
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
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .pro-card { transition: all 0.2s; border: 2px solid transparent; cursor: pointer; }
    .pro-card:hover { border-color: #d1d5db; background: #f9fafb; }
    .pro-card.selected { border-color: #000; background: #f9fafb; }
    .pro-checkbox { width: 20px; height: 20px; border-radius: 6px; border: 2px solid #d1d5db; transition: all 0.15s; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pro-card.selected .pro-checkbox { background: #000; border-color: #000; }
    .pro-card.selected .pro-checkbox svg { display: block; }
    .pro-card:not(.selected) .pro-checkbox svg { display: none; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Assign'; include '../includes/header.php'; ?>

    <?php
    $statusColors = [
      'Unassigned'  => 'bg-amber-50 text-amber-600',
      'Assigned'    => 'bg-blue-50 text-blue-600',
      'In Progress' => 'bg-violet-50 text-violet-600',
      'Completed'   => 'bg-emerald-50 text-emerald-600',
    ];
    $statusDots = [
      'Unassigned'  => 'bg-amber-400',
      'Assigned'    => 'bg-blue-400',
      'In Progress' => 'bg-violet-400',
      'Completed'   => 'bg-emerald-400',
    ];
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Assign</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage customer bookings and assign professionals</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="assign-search" type="text" placeholder="Search bookings..." oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Bookings</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="calendar" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Unassigned</p><p class="text-3xl font-bold text-gray-900"><?php echo $unassigned; ?></p><p class="text-xs text-gray-400 mt-0.5">Need action</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="alert-circle" class="w-5 h-5 text-amber-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">In Progress</p><p class="text-3xl font-bold text-gray-900"><?php echo $inProgress; ?></p><p class="text-xs text-gray-400 mt-0.5">Active now</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="loader" class="w-5 h-5 text-violet-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Completed</p><p class="text-3xl font-bold text-gray-900"><?php echo $completed; ?></p><p class="text-xs text-gray-400 mt-0.5">Done</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-1.5">
        <?php foreach(['all'=>'All','Unassigned'=>'Unassigned','Assigned'=>'Assigned','In Progress'=>'In Progress','Completed'=>'Completed'] as $k=>$v): ?>
        <button onclick="setTab('<?php echo $k; ?>')" id="tab-<?php echo str_replace(' ','-',$k); ?>"
          class="filter-tab text-sm font-medium px-4 py-2 <?php echo $k==='all'?'active':''; ?>">
          <?php echo $v; ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- Bookings Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Booking</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Customer</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Date & Slot</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="assign-tbody">
              <?php foreach($bookings as $b): ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $b['id']; ?>"
                  data-customer="<?php echo strtolower($b['customer']); ?>"
                  data-service="<?php echo strtolower($b['service']); ?>"
                  data-status="<?php echo $b['status']; ?>">
                <td class="px-5 py-4">
                  <span class="text-xs font-semibold text-gray-400">#<?php echo $b['id']; ?></span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $b['avatar']; ?>" class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-white shadow-sm" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?php echo $b['customer']; ?></p>
                      <p class="text-xs text-gray-400"><?php echo $b['city']; ?></p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm font-medium text-gray-900"><?php echo $b['service']; ?></p>
                  <p class="text-xs text-gray-400 mt-0.5"><?php echo $b['package']; ?></p>
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm font-medium text-gray-900"><?php echo date('d M Y', strtotime($b['date'])); ?></p>
                  <p class="text-xs text-gray-400 mt-0.5"><?php echo $b['slot']; ?></p>
                </td>
                <td class="px-5 py-4">
                  <?php if($b['professional']): ?>
                  <p class="text-sm font-medium text-gray-900"><?php echo $b['professional']; ?></p>
                  <?php else: ?>
                  <span class="text-xs text-gray-400 italic">Not assigned</span>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-4">
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $statusColors[$b['status']]; ?>">
                    <span class="w-1.5 h-1.5 rounded-full <?php echo $statusDots[$b['status']]; ?>"></span>
                    <?php echo $b['status']; ?>
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <?php if($b['status'] === 'Unassigned' || $b['status'] === 'Assigned'): ?>
                    <button onclick='openAssignDrawer(<?php echo json_encode($b); ?>)'
                      title="<?php echo $b['status'] === 'Unassigned' ? 'Assign Professional' : 'Reassign'; ?>"
                      class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg <?php echo $b['status'] === 'Unassigned' ? 'bg-black text-white hover:bg-gray-800' : 'border border-gray-200 text-gray-600 hover:bg-gray-100'; ?> text-xs font-medium transition-all">
                      <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                      <?php echo $b['status'] === 'Unassigned' ? 'Assign' : 'Reassign'; ?>
                    </button>
                    <?php elseif($b['status'] === 'In Progress'): ?>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-50 text-violet-600 text-xs font-medium">
                      <i data-lucide="loader" class="w-3.5 h-3.5"></i> In Progress
                    </span>
                    <?php else: ?>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-medium">
                      <i data-lucide="check" class="w-3.5 h-3.5"></i> Done
                    </span>
                    <?php endif; ?>
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
            <i data-lucide="clipboard-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No bookings found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter</p>
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

<!-- -- ASSIGN DRAWER -------------------------------------------------------- -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeAssignDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-lg bg-white z-50 shadow-2xl flex flex-col overflow-hidden">

  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <div>
      <h3 class="text-lg font-semibold text-gray-900">Assign Professional</h3>
      <p id="d-booking-label" class="text-sm text-gray-400 mt-0.5"></p>
    </div>
    <button onclick="closeAssignDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>

  <!-- Booking summary -->
  <div id="d-booking-summary" class="px-6 py-4 bg-gray-50/60 border-b border-gray-100 flex-shrink-0">
    <div class="flex items-center gap-4">
      <img id="d-customer-avatar" src="" class="w-12 h-12 rounded-2xl object-cover flex-shrink-0" alt="">
      <div class="flex-1 min-w-0">
        <p id="d-customer-name" class="text-sm font-semibold text-gray-900"></p>
        <p id="d-service-name" class="text-xs text-gray-500 mt-0.5"></p>
        <p id="d-booking-date" class="text-xs text-gray-400 mt-0.5"></p>
      </div>
      <span id="d-status-badge" class="text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0"></span>
    </div>
  </div>

  <!-- Professional search -->
  <div class="px-6 pt-5 pb-3 flex-shrink-0">
    <div class="flex items-center justify-between mb-3">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Select Professionals</p>
      <span id="selected-count" class="hidden text-xs font-semibold bg-black text-white px-2.5 py-0.5 rounded-full"></span>
    </div>
    <div class="relative">
      <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
      <input id="pro-search" type="text" placeholder="Search professionals..." oninput="filterPros()"
        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 transition-all">
    </div>
  </div>

  <!-- Professionals list -->
  <div class="flex-1 overflow-y-auto px-6 pb-4">
    <div id="pro-list" class="flex flex-col gap-2">
      <?php foreach($professionals as $p): ?>
      <div class="pro-card rounded-2xl p-4 flex items-center gap-4"
           data-pro-id="<?php echo $p['id']; ?>"
           data-pro-name="<?php echo strtolower($p['name']); ?>"
           onclick="selectPro(this, <?php echo $p['id']; ?>, '<?php echo $p['name']; ?>')">
        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0 text-sm font-bold text-gray-600">
          <?php echo strtoupper(substr($p['name'],0,1)); ?>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="text-sm font-semibold text-gray-900"><?php echo $p['name']; ?></p>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full <?php echo $p['category']==='Luxe'?'bg-violet-50 text-violet-600':'bg-amber-50 text-amber-600'; ?>">
              <?php echo $p['category']; ?>
            </span>
          </div>
          <div class="flex items-center gap-3 mt-0.5">
            <span class="text-xs text-gray-400 flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3"></i><?php echo $p['city']; ?></span>
            <span class="text-xs text-gray-400 flex items-center gap-1"><i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i><?php echo $p['rating']; ?></span>
          </div>
        </div>
        <div class="flex items-center gap-2.5 flex-shrink-0">
          <?php if($p['available']): ?>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Available
          </span>
          <?php else: ?>
          <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>Busy
          </span>
          <?php endif; ?>
          <div class="pro-checkbox">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Footer -->
  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <button onclick="closeAssignDrawer()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition-all">Cancel</button>
    <button id="confirm-assign-btn" onclick="confirmAssign()" disabled
      class="flex-1 py-2.5 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
      <i data-lucide="users" class="w-4 h-4"></i>
      <span id="confirm-assign-label">Select professionals</span>
    </button>
  </div>
</div>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  // Sidebar toggles are handled by sidebar.php
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  // -- Pagination -------------------------------------------------------------
  const ROWS_PER_PAGE = 8;
  let currentPage = 1, visibleRows = [], currentTab = 'all';

  function setTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab.replace(' ', '-')).classList.add('active');
    applyFilters();
  }

  function applyFilters() {
    const search = document.getElementById('assign-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#assign-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const textMatch = row.dataset.customer.includes(search) || row.dataset.service.includes(search);
      const tabMatch  = currentTab === 'all' || row.dataset.status === currentTab;
      return textMatch && tabMatch;
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} booking${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    const mk = (html, disabled, onClick) => { const b = document.createElement('button'); b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed'; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage===1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }));
  }

  // -- Assign Drawer ----------------------------------------------------------
  let currentBooking = null;
  let selectedPros = []; // Array of { id, name }

  const statusColors = {
    'Unassigned':  'bg-amber-50 text-amber-600',
    'Assigned':    'bg-blue-50 text-blue-600',
    'In Progress': 'bg-violet-50 text-violet-600',
    'Completed':   'bg-emerald-50 text-emerald-600',
  };

  function openAssignDrawer(booking) {
    currentBooking = booking;
    selectedPros = [];
    document.getElementById('d-booking-label').textContent = `Booking #${booking.id}`;
    document.getElementById('d-customer-avatar').src = booking.avatar;
    document.getElementById('d-customer-name').textContent = booking.customer;
    document.getElementById('d-service-name').textContent = booking.service + ' · ' + booking.package;
    document.getElementById('d-booking-date').textContent = booking.date + ' at ' + booking.slot + ' · ' + booking.city;
    const sb = document.getElementById('d-status-badge');
    sb.textContent = booking.status;
    sb.className = 'text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 ' + (statusColors[booking.status] || 'bg-gray-100 text-gray-500');
    // Reset all selections
    document.querySelectorAll('.pro-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('pro-search').value = '';
    filterPros();
    updateConfirmBtn();
    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeAssignDrawer() {
    document.getElementById('drawer-panel').classList.add('closed');
    document.getElementById('drawer-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
  }

  function selectPro(el, id, name) {
    const idx = selectedPros.findIndex(p => p.id === id);
    if (idx > -1) {
      // Deselect
      selectedPros.splice(idx, 1);
      el.classList.remove('selected');
    } else {
      // Select
      selectedPros.push({ id, name });
      el.classList.add('selected');
    }
    updateConfirmBtn();
  }

  function updateConfirmBtn() {
    const btn = document.getElementById('confirm-assign-btn');
    const lbl = document.getElementById('confirm-assign-label');
    const badge = document.getElementById('selected-count');
    const count = selectedPros.length;

    if (count > 0) {
      btn.disabled = false;
      badge.textContent = `${count} selected`;
      badge.classList.remove('hidden');
      if (count === 1) {
        lbl.textContent = `Assign ${selectedPros[0].name}`;
      } else {
        lbl.textContent = `Assign ${count} Professionals`;
      }
    } else {
      btn.disabled = true;
      lbl.textContent = 'Select professionals';
      badge.classList.add('hidden');
    }
  }

  function filterPros() {
    const q = document.getElementById('pro-search').value.toLowerCase();
    document.querySelectorAll('.pro-card').forEach(c => {
      c.style.display = c.dataset.proName.includes(q) ? '' : 'none';
    });
  }

  function confirmAssign() {
    if (selectedPros.length === 0 || !currentBooking) return;
    const names = selectedPros.map(p => p.name);
    const namesList = names.length === 1
      ? `<strong>${names[0]}</strong>`
      : names.slice(0, -1).map(n => `<strong>${n}</strong>`).join(', ') + ' and <strong>' + names[names.length - 1] + '</strong>';
    const proCount = names.length === 1 ? 'professional' : `${names.length} professionals`;

    Swal.fire({
      title: 'Confirm Assignment',
      html: `Assign ${namesList} to booking <strong>#${currentBooking.id}</strong> for <strong>${currentBooking.customer}</strong>?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#000',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: `Yes, Assign ${proCount}`
    }).then(r => {
      if (r.isConfirmed) {
        closeAssignDrawer();
        const msg = names.length === 1
          ? `${names[0]} has been assigned to booking #${currentBooking.id}.`
          : `${names.length} professionals have been assigned to booking #${currentBooking.id}.`;
        Swal.fire({ title: 'Assigned!', text: msg, icon: 'success', confirmButtonColor: '#000', timer: 2500, showConfirmButton: false });
      }
    });
  }

  // -- Init -------------------------------------------------------------------
  (function init() { visibleRows = Array.from(document.querySelectorAll('#assign-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
