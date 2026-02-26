@extends('layouts.app')

@section('content')
    @php
    $totalPros      = count($history);
    $totalEarnings  = array_sum(array_column($history, 'total_earnings'));
    $totalCommission= array_sum(array_column($history, 'total_commission'));
    $avgRating      = $totalPros > 0 ? round(array_sum(array_column($history, 'rating')) / $totalPros, 1) : 0;
    @endphp

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professional History</h2>
          <p class="text-sm text-gray-400 mt-0.5">Performance & payout history for all professionals</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="hist-search" type="text" placeholder="Search professional…" oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Professionals</p><p class="text-3xl font-bold text-gray-900">{{ $totalPros }}</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Total Earnings</p><p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalEarnings/1000, 0) }}K</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Commission</p><p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalCommission/1000, 0) }}K</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="percent" class="w-5 h-5 text-violet-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Avg Rating</p><p class="text-3xl font-bold text-gray-900">{{ $avgRating }} ★</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="star" class="w-5 h-5 text-amber-500"></i></div>
        </div>
      </div>

      <!-- Table Section -->
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
              @foreach($history as $h)
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $h['id'] }}"
                  data-name="{{ strtolower($h['name']) }}"
                  data-avatar="{{ $h['avatar'] }}"
                  data-category="{{ $h['category'] }}"
                  data-total-orders="{{ $h['total_orders'] }}"
                  data-completed="{{ $h['completed'] }}"
                  data-cancelled="{{ $h['cancelled'] }}"
                  data-total-earnings="{{ $h['total_earnings'] }}"
                  data-total-commission="{{ $h['total_commission'] }}"
                  data-payout-status="{{ $h['payout_status'] }}"
                  data-rating="{{ $h['rating'] }}"
                  data-monthly="{{ json_encode($h['monthly']) }}"
                  data-reviews="{{ json_encode($h['reviews']) }}"
                  onclick="openHistDrawer(this.dataset)">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="{{ $h['avatar'] }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white ring-offset-1 ring-offset-gray-100 flex-shrink-0" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">{{ $h['name'] }}</p>
                      <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $h['category']==='Luxe'?'bg-violet-50 text-violet-600':'bg-amber-50 text-amber-600' }}">{{ $h['category'] }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700">{{ $h['total_orders'] }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-emerald-600">{{ $h['completed'] }}</span>
                    <div class="progress-bar flex-1 min-w-[60px]"><div class="progress-fill" style="width:{{ round($h['completed']/$h['total_orders']*100) }}%"></div></div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-red-400">{{ $h['cancelled'] }}</td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($h['total_earnings']) }}</td>
                <td class="px-5 py-4 text-sm font-medium text-violet-600">₹{{ number_format($h['total_commission']) }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5">
                    <span class="text-sm font-semibold text-gray-900">{{ $h['rating'] }}</span>
                    <span class="stars text-sm">★</span>
                  </div>
                </td>
                <td class="px-5 py-4">
                  @php
                  $pc = match($h['payout_status']) { 'Paid'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                  @endphp
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $pc }}">{{ $h['payout_status'] }}</span>
                </td>
              </tr>
              @endforeach
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

    <!-- Monthly Earnings Chart -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Monthly Earnings Summary</p>
      <div id="hd-chart" class="flex items-end gap-1.5 h-24"></div>
      <div class="flex justify-between text-[10px] text-gray-400 mt-1.5 font-medium px-1">
        <span>J</span><span>F</span><span>M</span><span>A</span><span>M</span><span>J</span><span>J</span><span>A</span><span>S</span><span>O</span><span>N</span><span>D</span>
      </div>
    </div>

    <!-- Customer Reviews -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Customer Reviews</p>
      <div id="hd-reviews" class="flex flex-col gap-3"></div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .table-row { transition: background 0.15s; cursor: pointer; } .table-row:hover { background: #fafafa; }
    .drawer-panel { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .stars { color: #f59e0b; }
    .progress-bar { height: 6px; border-radius: 999px; background: #f3f4f6; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 999px; background: #000; transition: width 0.6s ease; }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
</style>
@endpush

@push('scripts')
<script>
  const ROWS_PER_PAGE = 10;
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
    const mk = (html, disabled, onClick) => { const b = document.createElement('button'); b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed'; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mk('<i data-lucide="chevron-left" class="w-4 h-4"></i>', currentPage === 1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mk('<i data-lucide="chevron-right" class="w-4 h-4"></i>', currentPage === totalPages || totalPages === 0, () => { currentPage++; renderPage(); }));
    lucide.createIcons();
  }

  function openHistDrawer(h) {
    document.getElementById('hd-avatar').src = h.avatar;
    document.getElementById('hd-name').textContent = h.name;
    document.getElementById('hd-category').textContent = h.category + ' Professional';
    document.getElementById('hd-total').textContent = h.totalOrders;
    document.getElementById('hd-completed').textContent = h.completed;
    document.getElementById('hd-cancelled').textContent = h.cancelled;
    document.getElementById('hd-earnings').textContent = '₹' + Number(h.totalEarnings).toLocaleString('en-IN');
    document.getElementById('hd-commission').textContent = '₹' + Number(h.totalCommission).toLocaleString('en-IN');
    document.getElementById('hd-payout').textContent = h.payoutStatus;
    document.getElementById('hd-rating').textContent = h.rating + ' ★';

    const starsEl = document.getElementById('hd-stars');
    starsEl.innerHTML = '';
    for (let i = 1; i <= 5; i++) {
      const s = document.createElement('span');
      s.className = 'text-xl ' + (i <= Math.round(h.rating) ? 'text-amber-400' : 'text-gray-200');
      s.textContent = '★'; starsEl.appendChild(s);
    }

    const monthly = JSON.parse(h.monthly);
    const chart = document.getElementById('hd-chart');
    chart.innerHTML = '';
    const max = Math.max(...monthly);
    monthly.forEach(val => {
      const bar = document.createElement('div');
      bar.className = 'flex-1 rounded-t-lg bg-gray-900 transition-all hover:bg-black cursor-help';
      bar.style.height = Math.max(4, Math.round((val / max) * 96)) + 'px';
      bar.title = '₹' + val.toLocaleString('en-IN');
      chart.appendChild(bar);
    });

    const reviews = JSON.parse(h.reviews);
    const revEl = document.getElementById('hd-reviews');
    revEl.innerHTML = reviews.map(r => `
      <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm font-semibold text-gray-900">${r.customer}</p>
          <span class="text-amber-400 text-xs">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span>
        </div>
        <p class="text-sm text-gray-500 italic">"${r.text}"</p>
      </div>`).join('');

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
  }

  function closeDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }
  (function init() { visibleRows = Array.from(document.querySelectorAll('#hist-tbody tr.table-row')); renderPage(); })();
</script>
@endpush
