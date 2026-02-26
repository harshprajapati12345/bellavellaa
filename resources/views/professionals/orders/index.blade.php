@extends('layouts.app')

@section('content')
    @php
    $totalOrders    = count($orders);
    $totalRevenue   = array_sum(array_column($orders, 'amount'));
    $totalCommission= array_sum(array_column($orders, 'commission'));
    $totalPayouts   = array_sum(array_column($orders, 'pro_earning'));
    @endphp

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professional Orders</h2>
        <p class="text-sm text-gray-400 mt-0.5">Track all orders assigned to professionals</p>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Orders</p><p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="shopping-bag" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Revenue</p><p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalRevenue) }}</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="indian-rupee" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Commission</p><p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalCommission) }}</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="percent" class="w-5 h-5 text-violet-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Pro Payouts</p><p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalPayouts) }}</p></div>
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
              @foreach($orders as $ord)
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $ord['id'] }}"
                  data-search="{{ strtolower($ord['customer'].' '.$ord['professional']) }}"
                  data-customer="{{ $ord['customer'] }}"
                  data-customer-phone="{{ $ord['customer_phone'] }}"
                  data-address="{{ $ord['address'] }}"
                  data-professional="{{ $ord['professional'] }}"
                  data-service="{{ $ord['service'] }}"
                  data-date="{{ $ord['date'] }}"
                  data-time="{{ $ord['time'] }}"
                  data-payment-method="{{ $ord['payment_method'] }}"
                  data-amount="{{ $ord['amount'] }}"
                  data-commission="{{ $ord['commission'] }}"
                  data-pro-earning="{{ $ord['pro_earning'] }}"
                  data-order-status="{{ $ord['order_status'] }}"
                  data-payment-status="{{ $ord['payment_status'] }}">
                <td class="px-5 py-4 text-sm font-mono font-medium text-gray-700">{{ $ord['id'] }}</td>
                <td class="px-5 py-4 text-sm text-gray-700 font-medium">{{ $ord['customer'] }}</td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $ord['professional'] }}</td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $ord['service'] }}</td>
                <td class="px-5 py-4 text-sm text-gray-500">{{ date('d M', strtotime($ord['date'])) }}</td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($ord['amount']) }}</td>
                <td class="px-5 py-4 text-sm text-violet-600 font-medium">₹{{ number_format($ord['commission']) }}</td>
                <td class="px-5 py-4 text-sm text-emerald-600 font-medium">₹{{ number_format($ord['pro_earning']) }}</td>
                <td class="px-5 py-4">
                  @php
                  $oc = match($ord['order_status']) { 'Completed'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', 'Accepted'=>'bg-blue-50 text-blue-600', default=>'bg-red-50 text-red-500' };
                  @endphp
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $oc }}">{{ $ord['order_status'] }}</span>
                </td>
                <td class="px-5 py-4">
                  @php
                  $pc = match($ord['payment_status']) { 'Online'=>'bg-blue-50 text-blue-600', 'COD'=>'bg-gray-100 text-gray-600', default=>'bg-orange-50 text-orange-500' };
                  @endphp
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $pc }}">{{ $ord['payment_status'] }}</span>
                </td>
                <td class="px-5 py-4 text-right">
                  <button onclick="openOrderDrawer(this.closest('tr').dataset)"
                    class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center ml-auto">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  </button>
                </td>
              </tr>
              @endforeach
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
@endsection

@push('styles')
<style>
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
</style>
@endpush

@push('scripts')
<script>
  const ROWS_PER_PAGE = 10;
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
    const mk = (html, disabled, onClick) => { const b = document.createElement('button'); b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed'; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mk('<i data-lucide="chevron-left" class="w-4 h-4"></i>', currentPage === 1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mk('<i data-lucide="chevron-right" class="w-4 h-4"></i>', currentPage === totalPages || totalPages === 0, () => { currentPage++; renderPage(); }));
    lucide.createIcons();
  }

  function openOrderDrawer(ord) {
    document.getElementById('od-id').textContent = ord.id;
    document.getElementById('od-customer').textContent = ord.customer;
    document.getElementById('od-customer-phone').textContent = ord.customerPhone;
    document.getElementById('od-address').textContent = ord.address;
    document.getElementById('od-professional').textContent = ord.professional;
    document.getElementById('od-service').textContent = ord.service;
    document.getElementById('od-date').textContent = new Date(ord.date).toLocaleDateString('en-IN', {day:'numeric',month:'short',year:'numeric'});
    document.getElementById('od-time').textContent = ord.time;
    document.getElementById('od-payment-method').textContent = ord.paymentMethod;
    document.getElementById('od-amount').textContent = '₹' + Number(ord.amount).toLocaleString('en-IN');
    document.getElementById('od-commission').textContent = '₹' + Number(ord.commission).toLocaleString('en-IN');
    document.getElementById('od-pro-earning').textContent = '₹' + Number(ord.proEarning).toLocaleString('en-IN');

    const osEl = document.getElementById('od-order-status');
    const ocMap = { 'Completed':'bg-emerald-50 text-emerald-600', 'Pending':'bg-amber-50 text-amber-600', 'Accepted':'bg-blue-50 text-blue-600', 'Cancelled':'bg-red-50 text-red-500' };
    osEl.textContent = ord.orderStatus; osEl.className = 'text-xs font-semibold px-3 py-1.5 rounded-full ' + (ocMap[ord.orderStatus] || 'bg-gray-100 text-gray-600');

    const psEl = document.getElementById('od-payment-status');
    const pcMap = { 'Online':'bg-blue-50 text-blue-600', 'COD':'bg-gray-100 text-gray-600', 'Refunded':'bg-orange-50 text-orange-500' };
    psEl.textContent = ord.paymentStatus; psEl.className = 'text-xs font-semibold px-3 py-1.5 rounded-full ' + (pcMap[ord.paymentStatus] || 'bg-gray-100 text-gray-600');

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
  }

  function closeDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }
  function cancelOrder() { Swal.fire({ title: 'Cancel Order?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Cancel' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Cancelled', icon: 'success', timer: 1500, showConfirmButton: false }); } }); }
  function refundOrder() { Swal.fire({ title: 'Process Refund?', icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Refund' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Refunded!', icon: 'success', timer: 1500, showConfirmButton: false }); } }); }
  function reassignOrder() { Swal.fire({ title: 'Reassign Professional', input: 'select', inputOptions: { 'Priya Sharma': 'Priya Sharma', 'Sunita Rao': 'Sunita Rao', 'Deepa Nair': 'Deepa Nair' }, showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Reassign' }).then(r => { if (r.isConfirmed) { closeDrawer(); Swal.fire({ title: 'Reassigned!', text: `Order assigned to ${r.value}`, icon: 'success', timer: 1500, showConfirmButton: false }); } }); }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#ord-tbody tr.table-row')); renderPage(); })();
</script>
@endpush
