<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professionals · Bellavella Admin</title>
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
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .avatar-ring { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #e5e7eb; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Professionals'; include '../includes/header.php'; ?>

    <?php
    $professionals = [
      ['id'=>1,'name'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Luxe','phone'=>'+91 98765 43210','city'=>'Mumbai','status'=>'Active','verification'=>'Verified','orders'=>142,'earnings'=>84200,'commission'=>15,'experience'=>'5 years','joined'=>'2023-06-12','services'=>['HD Bridal Makeup','Party Glam','Gold Facial'],'docs'=>true,'rating'=>4.9],
      ['id'=>2,'name'=>'Anjali Mehta','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','phone'=>'+91 87654 32109','city'=>'Delhi','status'=>'Active','verification'=>'Pending','orders'=>67,'earnings'=>32100,'commission'=>12,'experience'=>'3 years','joined'=>'2023-09-01','services'=>['Classic Haircut','Nail Art'],'docs'=>true,'rating'=>4.6],
      ['id'=>3,'name'=>'Sunita Rao','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Luxe','phone'=>'+91 76543 21098','city'=>'Bangalore','status'=>'Active','verification'=>'Verified','orders'=>211,'earnings'=>126600,'commission'=>15,'experience'=>'7 years','joined'=>'2023-03-20','services'=>['Hydra Facial','Aromatherapy Massage','Deep Tissue Massage'],'docs'=>true,'rating'=>4.8],
      ['id'=>4,'name'=>'Kavita Joshi','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','phone'=>'+91 65432 10987','city'=>'Pune','status'=>'Suspended','verification'=>'Verified','orders'=>34,'earnings'=>15300,'commission'=>12,'experience'=>'2 years','joined'=>'2024-01-05','services'=>['Express Grooming'],'docs'=>true,'rating'=>3.8],
      ['id'=>5,'name'=>'Meera Pillai','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Luxe','phone'=>'+91 54321 09876','city'=>'Chennai','status'=>'Active','verification'=>'Rejected','orders'=>0,'earnings'=>0,'commission'=>15,'experience'=>'4 years','joined'=>'2024-02-10','services'=>[],'docs'=>false,'rating'=>0],
      ['id'=>6,'name'=>'Deepa Nair','avatar'=>'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','phone'=>'+91 43210 98765','city'=>'Hyderabad','status'=>'Active','verification'=>'Verified','orders'=>89,'earnings'=>44500,'commission'=>12,'experience'=>'4 years','joined'=>'2023-11-15','services'=>['Weekend Rejuvenation','Nail Art Deluxe'],'docs'=>true,'rating'=>4.7],
    ];
    $total      = count($professionals);
    $verified   = count(array_filter($professionals, fn($p) => $p['verification'] === 'Verified'));
    $pending    = count(array_filter($professionals, fn($p) => $p['verification'] === 'Pending'));
    $suspended  = count(array_filter($professionals, fn($p) => $p['status'] === 'Suspended'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professionals Overview</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all registered beauty professionals</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="pro-search" type="text" placeholder="Name / Phone / City…" oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
          </div>
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Professional
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Professionals</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Verified</p><p class="text-3xl font-bold text-gray-900"><?php echo $verified; ?></p><p class="text-xs text-gray-400 mt-0.5">Approved</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="badge-check" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p><p class="text-3xl font-bold text-gray-900"><?php echo $pending; ?></p><p class="text-xs text-gray-400 mt-0.5">Awaiting review</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="clock" class="w-5 h-5 text-amber-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Suspended</p><p class="text-3xl font-bold text-gray-900"><?php echo $suspended; ?></p><p class="text-xs text-gray-400 mt-0.5">Restricted</p></div>
          <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0"><i data-lucide="ban" class="w-5 h-5 text-red-400"></i></div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-1.5">
        <?php foreach(['all'=>'All','verified'=>'Verified','pending'=>'Pending','rejected'=>'Rejected','active'=>'Active','suspended'=>'Suspended'] as $k=>$v): ?>
        <button onclick="setTab('<?php echo $k; ?>')" id="tab-<?php echo $k; ?>"
          class="filter-tab text-sm font-medium px-4 py-2 <?php echo $k==='all'?'active':''; ?>">
          <?php echo $v; ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1000px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">City</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Verification</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Orders</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Earnings</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="pro-tbody">
              <?php foreach($professionals as $pro): ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $pro['id']; ?>"
                  data-name="<?php echo strtolower($pro['name']); ?>"
                  data-phone="<?php echo $pro['phone']; ?>"
                  data-city="<?php echo strtolower($pro['city']); ?>"
                  data-status="<?php echo strtolower($pro['status']); ?>"
                  data-verification="<?php echo strtolower($pro['verification']); ?>">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $pro['avatar']; ?>" class="w-10 h-10 rounded-full object-cover avatar-ring flex-shrink-0" alt="<?php echo $pro['name']; ?>">
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?php echo $pro['name']; ?></p>
                      <p class="text-xs text-gray-400"><?php echo $pro['experience']; ?> exp.</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <?php if($pro['category']==='Luxe'): ?>
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-50 text-violet-600">Luxe</span>
                  <?php else: ?>
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">Prime</span>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $pro['phone']; ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5 text-sm text-gray-600">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i><?php echo $pro['city']; ?>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <?php if($pro['status']==='Active'): ?>
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Active</span>
                  <?php else: ?>
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-500"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Suspended</span>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-4">
                  <?php
                  $vClass = match($pro['verification']) {
                    'Verified' => 'bg-emerald-50 text-emerald-600',
                    'Pending'  => 'bg-amber-50 text-amber-600',
                    default    => 'bg-red-50 text-red-500'
                  };
                  $vIcon = match($pro['verification']) {
                    'Verified' => 'badge-check',
                    'Pending'  => 'clock',
                    default    => 'x-circle'
                  };
                  ?>
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $vClass; ?>">
                    <i data-lucide="<?php echo $vIcon; ?>" class="w-3 h-3"></i><?php echo $pro['verification']; ?>
                  </span>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700"><?php echo number_format($pro['orders']); ?></td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹<?php echo number_format($pro['earnings']); ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <button onclick='openDrawer(<?php echo json_encode($pro); ?>)' title="View Details"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick='openEditModal(<?php echo json_encode($pro); ?>)' title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick="toggleSuspend(<?php echo $pro['id']; ?>, '<?php echo $pro['status']; ?>')" title="<?php echo $pro['status']==='Active'?'Suspend':'Activate'; ?>"
                      class="w-8 h-8 rounded-lg border <?php echo $pro['status']==='Active'?'border-amber-100 text-amber-500 hover:bg-amber-500':'border-emerald-100 text-emerald-500 hover:bg-emerald-500'; ?> hover:text-white transition-all flex items-center justify-center">
                      <i data-lucide="<?php echo $pro['status']==='Active'?'pause-circle':'play-circle'; ?>" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick="deletePro(<?php echo $pro['id']; ?>)" title="Delete"
                      class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                      <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
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
            <i data-lucide="users-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No professionals found</p>
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

<!-- ── VIEW DRAWER ─────────────────────────────────────────────────────── -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-lg bg-white z-50 shadow-2xl flex flex-col overflow-hidden">
  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <h3 class="text-lg font-semibold text-gray-900">Professional Profile</h3>
    <button onclick="closeDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>
  <div class="flex-1 overflow-y-auto">
    <!-- Profile header -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-700 p-6 flex items-center gap-4">
      <img id="d-avatar" src="" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white/30" alt="">
      <div>
        <h2 id="d-name" class="text-xl font-bold text-white"></h2>
        <p id="d-category" class="text-sm text-gray-300 mt-0.5"></p>
        <div class="flex items-center gap-2 mt-2">
          <span id="d-status-badge" class="text-xs font-semibold px-2.5 py-1 rounded-full"></span>
          <span id="d-verification-badge" class="text-xs font-semibold px-2.5 py-1 rounded-full"></span>
        </div>
      </div>
    </div>

    <div class="p-6 flex flex-col gap-6">
      <!-- Stats -->
      <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <p id="d-orders" class="text-xl font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Orders</p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <p id="d-earnings" class="text-xl font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Earnings</p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
          <p id="d-rating" class="text-xl font-bold text-gray-900"></p>
          <p class="text-xs text-gray-400 mt-0.5">Rating</p>
        </div>
      </div>

      <!-- Contact -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Contact Details</p>
        <div class="space-y-2.5">
          <div class="flex items-center gap-3 text-sm text-gray-700"><i data-lucide="phone" class="w-4 h-4 text-gray-400 flex-shrink-0"></i><span id="d-phone"></span></div>
          <div class="flex items-center gap-3 text-sm text-gray-700"><i data-lucide="map-pin" class="w-4 h-4 text-gray-400 flex-shrink-0"></i><span id="d-city"></span></div>
          <div class="flex items-center gap-3 text-sm text-gray-700"><i data-lucide="calendar" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>Joined: <span id="d-joined" class="ml-1"></span></div>
          <div class="flex items-center gap-3 text-sm text-gray-700"><i data-lucide="briefcase" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>Experience: <span id="d-experience" class="ml-1"></span></div>
        </div>
      </div>

      <!-- Commission -->
      <div class="bg-black rounded-2xl p-4 flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Commission Rate</p>
          <p id="d-commission" class="text-2xl font-bold text-white"></p>
        </div>
        <button id="d-commission-btn" class="px-4 py-2 bg-white text-black text-sm font-semibold rounded-xl hover:bg-gray-100 transition-all">
          Change
        </button>
      </div>

      <!-- Services -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Assigned Services</p>
        <div id="d-services" class="flex flex-wrap gap-2"></div>
      </div>

      <!-- Documents -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Documents</p>
        <div id="d-docs-status" class="flex items-center gap-2 text-sm"></div>
      </div>

      <!-- Admin Actions -->
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Admin Actions</p>
        <div class="grid grid-cols-2 gap-3">
          <button id="d-suspend-btn" class="py-3 rounded-xl text-sm font-semibold border transition-all flex items-center justify-center gap-2"></button>
          <button id="d-block-btn" onclick="blockPro()"
            class="py-3 rounded-xl text-sm font-semibold border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
            <i data-lucide="shield-off" class="w-4 h-4"></i> Block
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <button id="d-edit-btn" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
      <i data-lucide="pencil" class="w-4 h-4"></i> Edit Profile
    </button>
    <button id="d-delete-btn" class="w-12 h-12 rounded-xl border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
      <i data-lucide="trash-2" class="w-4 h-4"></i>
    </button>
  </div>
</div>

<!-- ── EDIT MODAL ──────────────────────────────────────────────────────── -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
  <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between px-7 pt-7 pb-5 border-b border-gray-100 flex-shrink-0">
      <div><h3 id="modal-title" class="text-xl font-semibold text-gray-900">Edit Professional</h3><p class="text-sm text-gray-400 mt-0.5">Update professional details</p></div>
      <button onclick="closeEditModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all"><i data-lucide="x" class="w-4 h-4 text-gray-600"></i></button>
    </div>
    <div class="overflow-y-auto flex-1 px-7 py-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2"><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Full Name</label><input id="f-name" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white"></div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Phone</label><input id="f-phone" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white"></div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">City</label><input id="f-city" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white"></div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Category</label>
          <select id="f-category" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer">
            <option value="Luxe">Luxe</option><option value="Prime">Prime</option>
          </select>
        </div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Commission %</label>
          <div class="relative"><input id="f-commission" type="number" min="0" max="50" class="w-full px-4 py-3 pr-10 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white"><span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium pointer-events-none">%</span></div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Experience</label><input id="f-experience" type="text" placeholder="e.g. 3 years" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white"></div>
        <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">Status</label>
          <select id="f-status" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/50 text-sm bg-white cursor-pointer">
            <option value="Active">Active</option><option value="Suspended">Suspended</option>
          </select>
        </div>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 px-7 py-5 border-t border-gray-100 flex-shrink-0 bg-gray-50/50">
      <button onclick="closeEditModal()" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition-all">Cancel</button>
      <button onclick="saveEdit()" class="px-6 py-2.5 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/10">
        <i data-lucide="save" class="w-4 h-4"></i> Save Changes
      </button>
    </div>
  </div>
</div>

<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [], currentTab = 'all';

  function setTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    applyFilters();
  }

  function applyFilters() {
    const search = document.getElementById('pro-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#pro-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const nameMatch = row.dataset.name.includes(search) || row.dataset.phone.includes(search) || row.dataset.city.includes(search);
      let tabMatch = true;
      if (currentTab === 'verified')  tabMatch = row.dataset.verification === 'verified';
      if (currentTab === 'pending')   tabMatch = row.dataset.verification === 'pending';
      if (currentTab === 'rejected')  tabMatch = row.dataset.verification === 'rejected';
      if (currentTab === 'active')    tabMatch = row.dataset.status === 'active';
      if (currentTab === 'suspended') tabMatch = row.dataset.status === 'suspended';
      return nameMatch && tabMatch;
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} professional${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    const mkBtn = (html, disabled, onClick, extra='') => { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all ${extra}`; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mkBtn('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage===1, () => { currentPage--; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mkBtn('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
  }

  function openDrawer(pro) {
    document.getElementById('d-avatar').src = pro.avatar;
    document.getElementById('d-name').textContent = pro.name;
    document.getElementById('d-category').textContent = pro.category + ' Professional';
    document.getElementById('d-orders').textContent = pro.orders;
    document.getElementById('d-earnings').textContent = '₹' + Number(pro.earnings).toLocaleString('en-IN');
    document.getElementById('d-rating').textContent = pro.rating > 0 ? pro.rating + ' ★' : 'N/A';
    document.getElementById('d-phone').textContent = pro.phone;
    document.getElementById('d-city').textContent = pro.city;
    document.getElementById('d-joined').textContent = new Date(pro.joined).toLocaleDateString('en-IN', {day:'numeric',month:'long',year:'numeric'});
    document.getElementById('d-experience').textContent = pro.experience;
    document.getElementById('d-commission').textContent = pro.commission + '%';

    const sb = document.getElementById('d-status-badge');
    sb.textContent = pro.status;
    sb.className = 'text-xs font-semibold px-2.5 py-1 rounded-full ' + (pro.status === 'Active' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300');

    const vb = document.getElementById('d-verification-badge');
    vb.textContent = pro.verification;
    vb.className = 'text-xs font-semibold px-2.5 py-1 rounded-full ' + (pro.verification === 'Verified' ? 'bg-blue-500/20 text-blue-300' : pro.verification === 'Pending' ? 'bg-amber-500/20 text-amber-300' : 'bg-red-500/20 text-red-300');

    const svcWrap = document.getElementById('d-services');
    svcWrap.innerHTML = pro.services.length ? pro.services.map(s => `<span class="text-xs font-medium bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full">${s}</span>`).join('') : '<span class="text-sm text-gray-400">No services assigned</span>';

    const docsEl = document.getElementById('d-docs-status');
    docsEl.innerHTML = pro.docs
      ? '<span class="flex items-center gap-2 text-emerald-600 font-medium"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>Aadhaar & PAN uploaded</span>'
      : '<span class="flex items-center gap-2 text-red-500 font-medium"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>Documents not uploaded</span>';

    const suspBtn = document.getElementById('d-suspend-btn');
    if (pro.status === 'Active') {
      suspBtn.className = 'py-3 rounded-xl text-sm font-semibold border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center gap-2';
      suspBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg> Suspend';
      suspBtn.onclick = () => toggleSuspend(pro.id, 'Active');
    } else {
      suspBtn.className = 'py-3 rounded-xl text-sm font-semibold border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center gap-2';
      suspBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> Activate';
      suspBtn.onclick = () => toggleSuspend(pro.id, 'Suspended');
    }

    document.getElementById('d-commission-btn').onclick = () => changeCommission(pro.id, pro.commission);
    document.getElementById('d-edit-btn').onclick = () => { closeDrawer(); openEditModal(pro); };
    document.getElementById('d-delete-btn').onclick = () => deletePro(pro.id);

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

  function toggleSuspend(id, currentStatus) {
    const action = currentStatus === 'Active' ? 'Suspend' : 'Activate';
    Swal.fire({ title: `${action} Professional?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, ${action}` })
      .then(r => { if (r.isConfirmed) Swal.fire({ title: `${action}d!`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); });
  }

  function changeCommission(id, current) {
    Swal.fire({ title: 'Change Commission', input: 'number', inputValue: current, inputAttributes: { min: 0, max: 50, step: 1 }, showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Update', inputLabel: 'Commission %' })
      .then(r => { if (r.isConfirmed) Swal.fire({ title: 'Updated!', text: `Commission set to ${r.value}%`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); });
  }

  function blockPro() {
    Swal.fire({ title: 'Block Professional?', text: 'They will lose all access permanently.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Block' })
      .then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Blocked!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } });
  }

  function deletePro(id) {
    Swal.fire({ title: 'Delete Professional?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) Swal.fire({ title: 'Deleted!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); });
  }

  let editingPro = null;
  function openEditModal(pro) {
    editingPro = pro;
    document.getElementById('f-name').value = pro.name;
    document.getElementById('f-phone').value = pro.phone;
    document.getElementById('f-city').value = pro.city;
    document.getElementById('f-category').value = pro.category;
    document.getElementById('f-commission').value = pro.commission;
    document.getElementById('f-experience').value = pro.experience;
    document.getElementById('f-status').value = pro.status;
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-modal').classList.add('flex');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }
  function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); document.getElementById('edit-modal').classList.remove('flex'); document.body.style.overflow = ''; }
  function saveEdit() {
    const name = document.getElementById('f-name').value.trim();
    if (!name) { Swal.fire({ title: 'Missing Fields', text: 'Name is required.', icon: 'warning', confirmButtonColor: '#000' }); return; }
    closeEditModal();
    Swal.fire({ title: 'Saved!', text: `"${name}" updated.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
  }
  document.getElementById('edit-modal').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });

  (function init() { visibleRows = Array.from(document.querySelectorAll('#pro-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
