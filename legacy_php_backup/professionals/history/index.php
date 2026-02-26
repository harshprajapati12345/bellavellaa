<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professional History · Bellavella Admin</title>
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
    .table-row { transition: background 0.15s; cursor: pointer; } .table-row:hover { background: #fafafa; }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .stars { color: #f59e0b; }
    .progress-bar { height: 6px; border-radius: 999px; background: #f3f4f6; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 999px; background: #000; transition: width 0.6s ease; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Professional History'; include '../../includes/header.php'; ?>

    <?php
    $history = [
      ['id'=>1,'name'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Luxe','total_orders'=>142,'completed'=>138,'cancelled'=>4,'total_earnings'=>84200,'total_commission'=>12630,'rating'=>4.9,'payout_status'=>'Paid','monthly'=>[8200,9100,7800,10200,11000,9800,10500,12000,11500,13200,14000,12000],'reviews'=>[['customer'=>'Riya K.','text'=>'Absolutely stunning bridal makeup!','rating'=>5],['customer'=>'Aisha M.','text'=>'Very professional and skilled.','rating'=>5]]],
      ['id'=>2,'name'=>'Sunita Rao','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Luxe','total_orders'=>211,'completed'=>205,'cancelled'=>6,'total_earnings'=>126600,'total_commission'=>18990,'rating'=>4.8,'payout_status'=>'Paid','monthly'=>[9000,10500,11000,12000,13500,14000,12500,11000,13000,14500,15000,16000],'reviews'=>[['customer'=>'Divya S.','text'=>'Best massage experience ever!','rating'=>5],['customer'=>'Pooja V.','text'=>'Very relaxing and professional.','rating'=>4]]],
      ['id'=>3,'name'=>'Deepa Nair','avatar'=>'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','total_orders'=>89,'completed'=>85,'cancelled'=>4,'total_earnings'=>44500,'total_commission'=>5340,'rating'=>4.7,'payout_status'=>'Pending','monthly'=>[3500,4000,4200,4500,4800,5000,4700,4300,4600,4900,5200,5000],'reviews'=>[['customer'=>'Nisha P.','text'=>'Great nail art designs!','rating'=>5],['customer'=>'Sneha G.','text'=>'Very creative and neat.','rating'=>4]]],
      ['id'=>4,'name'=>'Anjali Mehta','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','total_orders'=>67,'completed'=>62,'cancelled'=>5,'total_earnings'=>32100,'total_commission'=>3852,'rating'=>4.6,'payout_status'=>'Paid','monthly'=>[2500,2800,3000,2700,3200,3500,3100,2900,3300,3600,3800,3500],'reviews'=>[['customer'=>'Pooja V.','text'=>'Good haircut, very satisfied.','rating'=>4],['customer'=>'Riya K.','text'=>'Professional and on time.','rating'=>5]]],
      ['id'=>5,'name'=>'Kavita Joshi','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','category'=>'Prime','total_orders'=>34,'completed'=>30,'cancelled'=>4,'total_earnings'=>15300,'total_commission'=>1836,'rating'=>3.8,'payout_status'=>'On Hold','monthly'=>[1200,1400,1500,1300,1600,1800,1700,1500,1600,1700,1800,1700],'reviews'=>[['customer'=>'Divya S.','text'=>'Decent service.','rating'=>3],['customer'=>'Aisha M.','text'=>'Could be better.','rating'=>4]]],
    ];
    $totalPros      = count($history);
    $totalEarnings  = array_sum(array_column($history, 'total_earnings'));
    $totalCommission= array_sum(array_column($history, 'total_commission'));
    $avgRating      = round(array_sum(array_column($history, 'rating')) / $totalPros, 1);
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professional History</h2>
          <p class="text-sm text-gray-400 mt-0.5">Performance & payout history for all professionals</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="hist-search" type="text" placeholder="Search professional..." oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Professionals</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalPros; ?></p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Total Earnings</p><p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalEarnings/1000, 0); ?>K</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Commission</p><p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalCommission/1000, 0); ?>K</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="percent" class="w-5 h-5 text-violet-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Avg Rating</p><p class="text-3xl font-bold text-gray-900"><?php echo $avgRating; ?> �...</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="star" class="w-5 h-5 text-amber-500"></i></div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Total Orders</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Completed</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Cancelled</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Total Earnings</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Commission</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Rating</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Payout</th>
              </tr>
            </thead>
            <tbody id="hist-tbody">
              <?php foreach($history as $h): ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $h['id']; ?>"
                  data-name="<?php echo strtolower($h['name']); ?>"
                  onclick='openHistDrawer(<?php echo json_encode($h); ?>)'>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $h['avatar']; ?>" class="w-10 h-10 rounded-full object-cover ring-2 ring-white ring-offset-1 ring-offset-gray-100 flex-shrink-0" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?php echo $h['name']; ?></p>
                      <span class="text-xs font-medium px-2 py-0.5 rounded-full <?php echo $h['category']==='Luxe'?'bg-violet-50 text-violet-600':'bg-amber-50 text-amber-600'; ?>"><?php echo $h['category']; ?></span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700"><?php echo $h['total_orders']; ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-emerald-600"><?php echo $h['completed']; ?></span>
                    <div class="progress-bar flex-1 min-w-[60px]"><div class="progress-fill" style="width:<?php echo round($h['completed']/$h['total_orders']*100); ?>%"></div></div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-red-400"><?php echo $h['cancelled']; ?></td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹<?php echo number_format($h['total_earnings']); ?></td>
                <td class="px-5 py-4 text-sm font-medium text-violet-600">₹<?php echo number_format($h['total_commission']); ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5">
                    <span class="text-sm font-semibold text-gray-900"><?php echo $h['rating']; ?></span>
                    <span class="stars text-sm">�...</span>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <?php
                  $pc = match($h['payout_status']) { 'Paid'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                  ?>
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $pc; ?>"><?php echo $h['payout_status']; ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <i data-lucide="history" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No history found</p>
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

<!-- ── HISTORY DRAWER ──────────────────────────────────────────────────── -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-lg bg-white z-50 shadow-2xl flex flex-col overflow-hidden">
  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <div class="flex items-center gap-3">
      <img id="hd-avatar" src="" class="w-10 h-10 rounded-full object-cover" alt="">
      <div><h3 id="hd-name" class="text-lg font-semibold text-gray-900"></h3><p id="hd-category" class="text-sm text-gray-400"></p></div>
    </div>
    <button onclick="closeDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>
  <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">

    <!-- Order Summary -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Order Summary</p>
      <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 rounded-2xl p-4 text-center"><p id="hd-total" class="text-xl font-bold text-gray-900"></p><p class="text-xs text-gray-400 mt-0.5">Total</p></div>
        <div class="bg-emerald-50 rounded-2xl p-4 text-center"><p id="hd-completed" class="text-xl font-bold text-emerald-600"></p><p class="text-xs text-gray-400 mt-0.5">Completed</p></div>
        <div class="bg-red-50 rounded-2xl p-4 text-center"><p id="hd-cancelled" class="text-xl font-bold text-red-500"></p><p class="text-xs text-gray-400 mt-0.5">Cancelled</p></div>
      </div>
    </div>

    <!-- Earnings & Commission -->
    <div class="bg-black rounded-2xl p-5 grid grid-cols-3 gap-4">
      <div class="text-center"><p id="hd-earnings" class="text-lg font-bold text-white"></p><p class="text-xs text-gray-400 mt-0.5">Earnings</p></div>
      <div class="text-center border-x border-white/10"><p id="hd-commission" class="text-lg font-bold text-violet-300"></p><p class="text-xs text-gray-400 mt-0.5">Commission</p></div>
      <div class="text-center"><p id="hd-payout" class="text-lg font-bold text-amber-300"></p><p class="text-xs text-gray-400 mt-0.5">Payout Status</p></div>
    </div>

    <!-- Rating -->
    <div class="bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Rating</p>
        <p id="hd-rating" class="text-3xl font-bold text-gray-900"></p>
      </div>
      <div class="flex gap-1" id="hd-stars"></div>
    </div>

    <!-- Monthly Earnings Chart (simple bar) -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Monthly Earnings Summary</p>
      <div id="hd-chart" class="flex items-end gap-1.5 h-24"></div>
      <div class="flex justify-between text-xs text-gray-400 mt-1.5">
        <?php foreach(['J','F','M','A','M','J','J','A','S','O','N','D'] as $m): ?>
        <span><?php echo $m; ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Customer Reviews -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Customer Reviews</p>
      <div id="hd-reviews" class="flex flex-col gap-3"></div>
    </div>

    <!-- Payout History -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Payout History</p>
      <div class="flex flex-col gap-2">
        <?php foreach(['Feb 2024'=>'₹14,000','Jan 2024'=>'₹12,000','Dec 2023'=>'₹13,200'] as $month=>$amount): ?>
        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
          <div class="flex items-center gap-2.5"><i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i><span class="text-sm text-gray-700"><?php echo $month; ?></span></div>
          <div class="flex items-center gap-2"><span class="text-sm font-semibold text-gray-900"><?php echo $amount; ?></span><span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">Paid</span></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  // Sidebar toggles are handled by sidebar.php

  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search = document.getElementById('hist-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#hist-tbody tr.table-row'));
    visibleRows = allRows.filter(row => row.dataset.name.includes(search));
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
    const prev = document.createElement('button');
    prev.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed';
    prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>';
    prev.disabled = currentPage === 1; prev.onclick = () => { currentPage--; renderPage(); }; btns.appendChild(prev);
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    const next = document.createElement('button');
    next.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed';
    next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
    next.disabled = currentPage === totalPages || totalPages === 0; next.onclick = () => { currentPage++; renderPage(); }; btns.appendChild(next);
  }

  function openHistDrawer(h) {
    document.getElementById('hd-avatar').src = h.avatar;
    document.getElementById('hd-name').textContent = h.name;
    document.getElementById('hd-category').textContent = h.category + ' Professional';
    document.getElementById('hd-total').textContent = h.total_orders;
    document.getElementById('hd-completed').textContent = h.completed;
    document.getElementById('hd-cancelled').textContent = h.cancelled;
    document.getElementById('hd-earnings').textContent = '₹' + Number(h.total_earnings).toLocaleString('en-IN');
    document.getElementById('hd-commission').textContent = '₹' + Number(h.total_commission).toLocaleString('en-IN');
    document.getElementById('hd-payout').textContent = h.payout_status;
    document.getElementById('hd-rating').textContent = h.rating + ' �...';

    // Stars
    const starsEl = document.getElementById('hd-stars');
    starsEl.innerHTML = '';
    for (let i = 1; i <= 5; i++) {
      const s = document.createElement('span');
      s.className = 'text-2xl ' + (i <= Math.round(h.rating) ? 'text-amber-400' : 'text-gray-200');
      s.textContent = '�...'; starsEl.appendChild(s);
    }

    // Mini bar chart
    const chart = document.getElementById('hd-chart');
    chart.innerHTML = '';
    const max = Math.max(...h.monthly);
    h.monthly.forEach(val => {
      const bar = document.createElement('div');
      bar.className = 'flex-1 rounded-t-lg bg-gray-900 transition-all';
      bar.style.height = Math.max(4, Math.round((val / max) * 96)) + 'px';
      bar.title = '₹' + val.toLocaleString('en-IN');
      chart.appendChild(bar);
    });

    // Reviews
    const revEl = document.getElementById('hd-reviews');
    revEl.innerHTML = h.reviews.map(r => `
      <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm font-semibold text-gray-900">${r.customer}</p>
          <span class="text-amber-400 text-sm">${'�...'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span>
        </div>
        <p class="text-sm text-gray-500">"${r.text}"</p>
      </div>`).join('');

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }
  (function init() { visibleRows = Array.from(document.querySelectorAll('#hist-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
