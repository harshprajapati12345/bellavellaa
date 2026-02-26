<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offers � Bellavella Admin</title>
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
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Offers'; include '../includes/header.php'; ?>

    <?php
    $offers = [
      ['id'=>1, 'name'=>'First Booking Discount', 'code'=>'WELCOME200', 'discount'=>'₹200', 'type'=>'Flat', 'status'=>'Active', 'usage'=>145, 'expiry'=>'2024-12-31'],
      ['id'=>2, 'name'=>'Summer Bridal Sale',    'code'=>'BRIDE20',   'discount'=>'20%', 'type'=>'Percentage', 'status'=>'Active', 'usage'=>82,  'expiry'=>'2024-06-30'],
      ['id'=>3, 'name'=>'Weekend Glow',           'code'=>'GLOW500',   'discount'=>'₹500', 'type'=>'Flat', 'status'=>'Inactive', 'usage'=>29,  'expiry'=>'2024-03-15'],
      ['id'=>4, 'name'=>'Referral Bonus',        'code'=>'REF50',     'discount'=>'50%', 'type'=>'Percentage', 'status'=>'Active', 'usage'=>210, 'expiry'=>'2024-12-31'],
    ];
    $total    = count($offers);
    $active   = count(array_filter($offers, fn($o) => $o['status']==='Active'));
    $totalUsage = array_sum(array_column($offers, 'usage'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Promotional Offers</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage discounts, coupons and seasonal offers</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" placeholder="Search offers..." 
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
          </div>
          <a href="create.php"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Offer
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900"><?php echo $total; ?></p><p class="text-xs text-gray-400 mt-0.5">Active & Inactive</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="tag" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Live Now</p><p class="text-3xl font-bold text-gray-900"><?php echo $active; ?></p><p class="text-xs text-gray-400 mt-0.5">Current Offers</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">Total Usage</p><p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalUsage); ?></p><p class="text-xs text-gray-400 mt-0.5">All time claims</p></div>
          <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0"><i data-lucide="ticket" class="w-5 h-5 text-blue-500"></i></div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">ID</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Offer Details</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Code</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Discount</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Usage</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Expiry</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($offers as $o): ?>
              <tr class="table-row border-b border-gray-50">
                <td class="px-6 py-4 text-sm text-gray-400 font-medium">#<?php echo $o['id']; ?></td>
                <td class="px-4 py-4">
                  <span class="text-sm font-semibold text-gray-900"><?php echo $o['name']; ?></span>
                </td>
                <td class="px-4 py-4">
                  <span class="text-xs font-mono bg-gray-100 px-3 py-1 rounded-lg text-gray-600 font-bold border border-gray-200"><?php echo $o['code']; ?></span>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900"><?php echo $o['discount']; ?></span>
                    <span class="text-[10px] text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100"><?php echo $o['type']; ?></span>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <span class="text-sm font-medium text-gray-700"><?php echo $o['usage']; ?></span>
                </td>
                <td class="px-4 py-4 text-sm text-gray-500"><?php echo date('d M Y', strtotime($o['expiry'])); ?></td>
                <td class="px-4 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" <?php echo $o['status']==='Active'?'checked':''; ?> onchange="toggleStatus(<?php echo $o['id']; ?>, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td class="px-4 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="edit.php?id=<?php echo $o['id']; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    <button onclick="confirmDelete(<?php echo $o['id']; ?>)" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  function toggleStatus(id, el) {
    const s = el.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${s}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000' })
      .then(r => { if (!r.isConfirmed) el.checked = !el.checked; });
  }

  function confirmDelete(id) {
    Swal.fire({ title: 'Delete Offer?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444' })
      .then(r => { if (r.isConfirmed) Swal.fire('Deleted!', '', 'success'); });
  }
</script>
</body>
</html>
