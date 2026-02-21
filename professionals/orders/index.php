<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professional Orders · Bellavella Admin</title>
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
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Professional Orders'; include '../../includes/header.php'; ?>

    <?php
    $orders = [
      ['id'=>'ORD-1001','customer'=>'Riya Kapoor','customer_phone'=>'+91 98001 11111','professional'=>'Priya Sharma','service'=>'HD Bridal Makeup','date'=>'2024-02-15','time'=>'10:00 AM','amount'=>4500,'commission'=>675,'pro_earning'=>3825,'order_status'=>'Completed','payment_status'=>'Online','address'=>'12, Rose Garden, Andheri West, Mumbai','payment_method'=>'UPI'],
      ['id'=>'ORD-1002','customer'=>'Sneha Gupta','customer_phone'=>'+91 98002 22222','professional'=>'Sunita Rao','service'=>'Hydra Facial','date'=>'2024-02-14','time'=>'2:00 PM','amount'=>2800,'commission'=>420,'pro_earning'=>2380,'order_status'=>'Completed','payment_status'=>'Online','address'=>'45, MG Road, Bangalore','payment_method'=>'Card'],
      ['id'=>'ORD-1003','customer'=>'Pooja Verma','customer_phone'=>'+91 98003 33333','professional'=>'Anjali Mehta','service'=>'Classic Haircut','date'=>'2024-02-16','time'=>'11:30 AM','amount'=>800,'commission'=>96,'pro_earning'=>704,'order_status'=>'Pending','payment_status'=>'COD','address'=>'7, Lajpat Nagar, Delhi','payment_method'=>'Cash'],
      ['id'=>'ORD-1004','customer'=>'Nisha Patel','customer_phone'=>'+91 98004 44444','professional'=>'Deepa Nair','service'=>'Nail Art Deluxe','date'=>'2024-02-13','time'=>'4:00 PM','amount'=>1500,'commission'=>180,'pro_earning'=>1320,'order_status'=>'Cancelled','payment_status'=>'Refunded','address'=>'23, Banjara Hills, Hyderabad','payment_method'=>'UPI'],
      ['id'=>'ORD-1005','customer'=>'Aisha Khan','customer_phone'=>'+91 98005 55555','professional'=>'Priya Sharma','service'=>'Party Glam','date'=>'2024-02-17','time'=>'6:00 PM','amount'=>3200,'commission'=>480,'pro_earning'=>2720,'order_status'=>'Accepted','payment_status'=>'Online','address'=>'8, Juhu, Mumbai','payment_method'=>'Card'],
      ['id'=>'ORD-1006','customer'=>'Divya Sharma','customer_phone'=>'+91 98006 66666','professional'=>'Sunita Rao','service'=>'Deep Tissue Massage','date'=>'2024-02-12','time'=>'3:00 PM','amount'=>3500,'commission'=>525,'pro_earning'=>2975,'order_status'=>'Completed','payment_status'=>'Online','address'=>'56, Koramangala, Bangalore','payment_method'=>'UPI'],
    ];
    $totalOrders    = count($orders);
    $totalRevenue   = array_sum(array_column($orders, 'amount'));
    $totalCommission= array_sum(array_column($orders, 'commission'));
    $totalPayouts   = array_sum(array_column($orders, 'pro_earning'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professional Orders</h2>
        <p class="text-sm text-gray-400 mt-0.5">Track all orders assigned to professionals</p>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Orders</p><p class="text-3xl font-bold text-gray-900"><?php echo $totalOrders; ?></p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="shopping-bag" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Revenue</p><p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalRevenue); ?></p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="indian-rupee" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Commission</p><p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalCommission); ?></p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="percent" class="w-5 h-5 text-violet-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Pro Payouts</p><p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalPayouts); ?></p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="wallet" class="w-5 h-5 text-amber-500"></i></div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[180px]">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="ord-search" type="text" placeholder="Professional / Customer…" oninput="applyFilters()"
            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40">
        </div>
        <select id="f-order-status" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Statuses</option>
          <option value="Pending">Pending</option>
          <option value="Accepted">Accepted</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
        <select id="f-payment-status" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Payments</option>
          <option value="Online">Online</option>
          <option value="COD">COD</option>
          <option value="Refunded">Refunded</option>
        </select>
        <button onclick="resetFilters()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-100 transition-all">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
        </button>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1100px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Customer</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Date</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Amount</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Commission</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Pro Earning</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Order Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Payment</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="ord-tbody">
              <?php foreach($orders as $ord): ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $ord['id']; ?>"
                  data-search="<?php echo strtolower($ord['customer'].' '.$ord['professional']); ?>"
                  data-order-status="<?php echo $ord['order_status']; ?>"
                  data-payment-status="<?php echo $ord['payment_status']; ?>">
                <td class="px-5 py-4 text-sm font-mono font-medium text-gray-700"><?php echo $ord['id']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-700 font-medium"><?php echo $ord['customer']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $ord['professional']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $ord['service']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-500"><?php echo date('d M', strtotime($ord['date'])); ?></td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹<?php echo number_format($ord['amount']); ?></td>
                <td class="px-5 py-4 text-sm text-violet-600 font-medium">₹<?php echo number_format($ord['commission']); ?></td>
                <td class="px-5 py-4 text-sm text-emerald-600 font-medium">₹<?php echo number_format($ord['pro_earning']); ?></td>
                <td class="px-5 py-4">
                  <?php
                  $oc = match($ord['order_status']) { 'Completed'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', 'Accepted'=>'bg-blue-50 text-blue-600', default=>'bg-red-50 text-red-500' };
                  ?>
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $oc; ?>"><?php echo $ord['order_status']; ?></span>
                </td>
                <td class="px-5 py-4">
                  <?php
                  $pc = match($ord['payment_status']) { 'Online'=>'bg-blue-50 text-blue-600', 'COD'=>'bg-gray-100 text-gray-600', default=>'bg-orange-50 text-orange-500' };
                  ?>
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $pc; ?>"><?php echo $ord['payment_status']; ?></span>
                </td>
                <td class="px-5 py-4 text-right">
                  <button onclick='openOrderDrawer(<?php echo json_encode($ord); ?>)'
                    class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center ml-auto">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  </button>
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
          <p class="text-gray-500 font-medium">No orders found</p>
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

<!-- ── ORDER DETAIL DRAWER ─────────────────────────────────────────────── -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">
  <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-gray-100 flex-shrink-0">
    <div>
      <h3 id="od-id" class="text-lg font-semibold text-gray-900"></h3>
      <p class="text-sm text-gray-400">Order Details</p>
    </div>
    <button onclick="closeDrawer()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
    </button>
  </div>
  <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-5">

    <!-- Status badges -->
    <div class="flex items-center gap-2">
      <span id="od-order-status" class="text-xs font-semibold px-3 py-1.5 rounded-full"></span>
      <span id="od-payment-status" class="text-xs font-semibold px-3 py-1.5 rounded-full"></span>
    </div>

    <!-- Financials -->
    <div class="bg-black rounded-2xl p-5 grid grid-cols-3 gap-4">
      <div class="text-center"><p id="od-amount" class="text-xl font-bold text-white"></p><p class="text-xs text-gray-400 mt-0.5">Total</p></div>
      <div class="text-center border-x border-white/10"><p id="od-commission" class="text-xl font-bold text-violet-300"></p><p class="text-xs text-gray-400 mt-0.5">Commission</p></div>
      <div class="text-center"><p id="od-pro-earning" class="text-xl font-bold text-emerald-300"></p><p class="text-xs text-gray-400 mt-0.5">Pro Payout</p></div>
    </div>

    <!-- Customer -->
    <div class="bg-gray-50 rounded-2xl p-4">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Customer</p>
      <p id="od-customer" class="text-sm font-semibold text-gray-900"></p>
      <p id="od-customer-phone" class="text-sm text-gray-500 mt-0.5"></p>
      <div class="flex items-start gap-2 mt-2 text-sm text-gray-500"><i data-lucide="map-pin" class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"></i><span id="od-address"></span></div>
    </div>

    <!-- Professional -->
    <div class="bg-gray-50 rounded-2xl p-4">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Professional</p>
      <p id="od-professional" class="text-sm font-semibold text-gray-900"></p>
    </div>

    <!-- Service & Booking -->
    <div class="bg-gray-50 rounded-2xl p-4">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Service & Booking</p>
      <p id="od-service" class="text-sm font-semibold text-gray-900"></p>
      <div class="flex items-center gap-4 mt-2">
        <div class="flex items-center gap-1.5 text-sm text-gray-500"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i><span id="od-date"></span></div>
        <div class="flex items-center gap-1.5 text-sm text-gray-500"><i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i><span id="od-time"></span></div>
      </div>
      <div class="flex items-center gap-1.5 text-sm text-gray-500 mt-1.5"><i data-lucide="credit-card" class="w-3.5 h-3.5 text-gray-400"></i><span id="od-payment-method"></span></div>
    </div>

    <!-- Admin Actions -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Admin Actions</p>
      <div class="grid grid-cols-3 gap-2">
        <button onclick="cancelOrder()" class="py-2.5 rounded-xl text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-1.5">
          <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Cancel
        </button>
        <button onclick="refundOrder()" class="py-2.5 rounded-xl text-xs font-semibold border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center gap-1.5">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Refund
        </button>
        <button onclick="reassignOrder()" class="py-2.5 rounded-xl text-xs font-semibold border border-blue-200 text-blue-600 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center gap-1.5">
          <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Reassign
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  function toggleProfessionals() { document.getElementById('professionals-submenu').classList.toggle('open'); document.getElementById('professionals-chevron').classList.toggle('chevron-rotate'); }
  function toggleMedia() { document.getElementById('media-submenu').classList.toggle('open'); document.getElementById('media-chevron').classList.toggle('chevron-rotate'); }

  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search = document.getElementById('ord-search').value.toLowerCase();
    const os = document.getElementById('f-order-status').value;
    const ps = document.getElementById('f-payment-status').value;
    const allRows = Array.from(document.querySelectorAll('#ord-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const sm = row.dataset.search.includes(search);
      const om = !os || row.dataset.orderStatus === os;
      const pm = !ps || row.dataset.paymentStatus === ps;
      return sm && om && pm;
    });
    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function resetFilters() {
    document.getElementById('ord-search').value = '';
    document.getElementById('f-order-status').value = '';
    document.getElementById('f-payment-status').value = '';
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} order${total !== 1 ? 's' : ''}`;
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

  function openOrderDrawer(ord) {
    document.getElementById('od-id').textContent = ord.id;
    document.getElementById('od-customer').textContent = ord.customer;
    document.getElementById('od-customer-phone').textContent = ord.customer_phone;
    document.getElementById('od-address').textContent = ord.address;
    document.getElementById('od-professional').textContent = ord.professional;
    document.getElementById('od-service').textContent = ord.service;
    document.getElementById('od-date').textContent = new Date(ord.date).toLocaleDateString('en-IN', {day:'numeric',month:'short',year:'numeric'});
    document.getElementById('od-time').textContent = ord.time;
    document.getElementById('od-payment-method').textContent = ord.payment_method;
    document.getElementById('od-amount').textContent = '₹' + Number(ord.amount).toLocaleString('en-IN');
    document.getElementById('od-commission').textContent = '₹' + Number(ord.commission).toLocaleString('en-IN');
    document.getElementById('od-pro-earning').textContent = '₹' + Number(ord.pro_earning).toLocaleString('en-IN');

    const osEl = document.getElementById('od-order-status');
    const ocMap = { 'Completed':'bg-emerald-50 text-emerald-600', 'Pending':'bg-amber-50 text-amber-600', 'Accepted':'bg-blue-50 text-blue-600', 'Cancelled':'bg-red-50 text-red-500' };
    osEl.textContent = ord.order_status; osEl.className = 'text-xs font-semibold px-3 py-1.5 rounded-full ' + (ocMap[ord.order_status] || 'bg-gray-100 text-gray-600');

    const psEl = document.getElementById('od-payment-status');
    const pcMap = { 'Online':'bg-blue-50 text-blue-600', 'COD':'bg-gray-100 text-gray-600', 'Refunded':'bg-orange-50 text-orange-500' };
    psEl.textContent = ord.payment_status; psEl.className = 'text-xs font-semibold px-3 py-1.5 rounded-full ' + (pcMap[ord.payment_status] || 'bg-gray-100 text-gray-600');

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  function closeDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }
  function cancelOrder() { Swal.fire({ title: 'Cancel Order?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Cancel' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Cancelled', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } }); }
  function refundOrder() { Swal.fire({ title: 'Process Refund?', icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Refund' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Refunded!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } }); }
  function reassignOrder() { Swal.fire({ title: 'Reassign Professional', input: 'select', inputOptions: { 'Priya Sharma': 'Priya Sharma', 'Sunita Rao': 'Sunita Rao', 'Deepa Nair': 'Deepa Nair' }, showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Reassign' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Reassigned!', text: `Order assigned to ${r.value}`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } }); }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#ord-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>
