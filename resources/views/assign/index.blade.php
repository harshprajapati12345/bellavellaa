@extends('layouts.app')
@php $pageTitle = 'Assign'; @endphp

@section('content')
  @php
    $bookings = $bookings ?? collect();
    $professionals = $professionals ?? collect();
    $total = $bookings->count();
    $unassigned = $bookings->where('status', 'Unassigned')->count();
    $inProgress = $bookings->where('status', 'In Progress')->count();
    $completed = $bookings->where('status', 'Completed')->count();
    $statusColors = ['Unassigned' => 'bg-amber-50 text-amber-600', 'Assigned' => 'bg-blue-50 text-blue-600', 'In Progress' => 'bg-violet-50 text-violet-600', 'Completed' => 'bg-emerald-50 text-emerald-600'];
    $statusDots = ['Unassigned' => 'bg-amber-400', 'Assigned' => 'bg-blue-400', 'In Progress' => 'bg-violet-400', 'Completed' => 'bg-emerald-400'];
  @endphp

  <div class="flex flex-col gap-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Assign</h2>
        <p class="text-sm text-gray-400 mt-0.5">Manage customer bookings and assign professionals</p>
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm">
          <span class="text-sm font-medium text-gray-700">Auto Assign</span>
          <label class="toggle-switch">
            <input type="checkbox" id="auto-assign-toggle" onchange="toggleAutoAssign()">
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="relative">
          <i data-lucide="search"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="assign-search" type="text" placeholder="Search bookings…" oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p>
          <p class="text-3xl font-bold text-gray-900">{{ $total }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Bookings</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i
            data-lucide="calendar" class="w-5 h-5 text-gray-600"></i></div>
      </div>
      <div
        class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Unassigned</p>
          <p class="text-3xl font-bold text-gray-900">{{ $unassigned }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Need action</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i
            data-lucide="alert-circle" class="w-5 h-5 text-amber-500"></i></div>
      </div>
      <div
        class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">In Progress</p>
          <p class="text-3xl font-bold text-gray-900">{{ $inProgress }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Active now</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i
            data-lucide="loader" class="w-5 h-5 text-violet-500"></i></div>
      </div>
      <div
        class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Completed</p>
          <p class="text-3xl font-bold text-gray-900">{{ $completed }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Done</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i
            data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-1.5">
      @foreach(['all' => 'All', 'Unassigned' => 'Unassigned', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Completed' => 'Completed'] as $k => $v)
        <button onclick="setTab('{{ $k }}')" id="tab-{{ Str::slug($k) }}"
          class="filter-tab text-sm font-medium px-4 py-2 {{ $k === 'all' ? 'active' : '' }}">
          {{ $v }}
        </button>
      @endforeach
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
              <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Date & Slot
              </th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional
              </th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
              <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
            </tr>
          </thead>
          <tbody id="assign-tbody">
            @foreach($bookings as $b)
              <tr class="table-row border-b border-gray-50" data-id="{{ $b->id }}"
                data-customer="{{ strtolower($b->customer->name ?? $b->customer_name ?? '') }}"
                data-display-name="{{ $b->customer->name ?? $b->customer_name }}"
                data-service="{{ strtolower($b->service->name ?? $b->service_name ?? '') }}"
                data-display-service="{{ $b->service->name ?? $b->service_name }}"
                data-package="{{ $b->package->name ?? $b->package_name ?? '' }}" data-status="{{ $b->status }}"
                data-avatar="{{ $b->customer->avatar ?? $b->customer_avatar ?? 'https://i.pravatar.cc/64?img=' . ($b->id % 50) }}"
                data-city="{{ $b->city ?? '—' }}"
                data-date="{{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d M Y') : '—' }}"
                data-slot="{{ $b->slot ?? '' }}"
                data-professional="{{ $b->professional->name ?? $b->professional_name ?? '' }}">
                <td class="px-5 py-4"><span class="text-xs font-semibold text-gray-400">#{{ $b->id }}</span></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      src="{{ $b->customer->avatar ?? $b->customer_avatar ?? 'https://i.pravatar.cc/64?img=' . ($b->id % 50) }}"
                      class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-white shadow-sm" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">{{ $b->customer->name ?? $b->customer_name }}</p>
                      <p class="text-xs text-gray-400">{{ $b->city ?? '—' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm font-medium text-gray-900">{{ $b->service->name ?? $b->service_name }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ $b->package->name ?? $b->package_name ?? '' }}</p>
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm font-medium text-gray-900">
                    {{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d M Y') : '—' }}
                  </p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ $b->slot ?? '' }}</p>
                </td>
                <td class="px-5 py-4">
                  @if($b->professional->name ?? $b->professional_name)
                    <p class="text-sm font-medium text-gray-900">{{ $b->professional->name ?? $b->professional_name }}</p>
                  @else
                    <span class="text-xs text-gray-400 italic">Not assigned</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors[$b->status] ?? 'bg-gray-100 text-gray-500' }}">
                    <span
                      class="w-1.5 h-1.5 rounded-full {{ $statusDots[$b->status] ?? 'bg-gray-300' }}"></span>{{ $b->status }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <button type="button" title="View Booking"
                      class="view-drawer-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center"
                      data-title="Booking #{{ $b->id }} - {{ $b->customer_name ?? $b->customer }}"
                      data-description="Service: {{ $b->service_name ?? $b->service }} {{ $b->package ? '(Package: ' . $b->package . ')' : '' }}\nCity: {{ $b->city ?? '—' }}\nSlot: {{ $b->slot ?? '—' }}"
                      data-created="{{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d M Y') : '—' }}">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    @if($b->status === 'Unassigned' || $b->status === 'Assigned')
                      <button type="button" onclick="openAssignDrawer(this.closest('tr').dataset)"
                        title="{{ $b->status === 'Unassigned' ? 'Assign Professional' : 'Reassign' }}"
                        class="manual-assign-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $b->status === 'Unassigned' ? 'bg-black text-white hover:bg-gray-800' : 'border border-gray-200 text-gray-600 hover:bg-gray-100' }} text-xs font-medium transition-all">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        {{ $b->status === 'Unassigned' ? 'Assign' : 'Reassign' }}
                      </button>

                      @if($b->status === 'Unassigned')
                        <span
                          class="auto-assign-badge flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-xs font-medium border border-amber-100/50">
                          <i data-lucide="zap" class="w-3.5 h-3.5 fill-amber-500"></i> Auto Assigning...
                        </span>
                      @endif
                    @elseif($b->status === 'In Progress')
                      <span
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-50 text-violet-600 text-xs font-medium"><i
                          data-lucide="loader" class="w-3.5 h-3.5"></i> In Progress</span>
                    @else
                      <span
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-medium"><i
                          data-lucide="check" class="w-3.5 h-3.5"></i> Done</span>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto"><i
            data-lucide="clipboard-x" class="w-8 h-8 text-gray-300"></i></div>
        <p class="text-gray-500 font-medium">No bookings found</p>
        <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter</p>
      </div>

      <!-- Pagination -->
      <div id="pagination-wrap"
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
        <p id="pagination-info" class="text-sm text-gray-400"></p>
        <div id="pagination-btns" class="flex items-center gap-1.5"></div>
      </div>
    </div>

  </div>

  </div>
  </div>

  </div>
  </div>

  <!-- ── ASSIGN PROFESSIONAL DRAWER ────────────────────────────────────────── -->
  <div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeAssignDrawer()">
  </div>
  <div id="drawer-panel"
    class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">

    <div class="px-6 pt-6 pb-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
      <div>
        <h3 class="text-lg font-bold text-gray-900 leading-tight">Assign Professional</h3>
        <p id="d-booking-label" class="text-xs text-gray-400 mt-0.5 uppercase tracking-widest font-mono"></p>
      </div>
      <button onclick="closeAssignDrawer()"
        class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center transition-all">
        <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
      </button>
    </div>

    <!-- Booking Summary Mini Card -->
    <div class="p-6 bg-gray-50/50 flex-shrink-0 border-b border-gray-100">
      <div class="flex items-center gap-4">
        <img id="d-customer-avatar" src="" class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-sm" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between gap-2 mb-0.5">
            <h4 id="d-customer-name" class="text-sm font-bold text-gray-900 truncate"></h4>
            <span id="d-status-badge"></span>
          </div>
          <p id="d-service-name" class="text-xs text-gray-600 truncate"></p>
          <p id="d-booking-date" class="text-[10px] text-gray-400 mt-0.5"></p>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="p-6 border-b border-gray-50 flex-shrink-0">
      <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
        <input type="text" id="pro-search" placeholder="Search professionals..." oninput="filterPros()"
          class="w-full pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-black/20 transition-all">
      </div>
    </div>

    <!-- Professionals List -->
    <div class="flex-1 overflow-y-auto p-6 space-y-3" id="pro-list">
      @foreach($professionals as $pro)
        <div
          class="pro-card p-4 rounded-2xl border-2 border-transparent bg-white shadow-sm ring-1 ring-gray-100 flex items-center gap-4 hover:shadow-md transition-all"
          data-pro-name="{{ strtolower($pro->name) }}"
          onclick="selectPro(this, {{ $pro->id }}, '{{ addslashes($pro->name) }}')">
          <div class="pro-checkbox">
            <i data-lucide="check" class="w-3 h-3 text-white"></i>
          </div>
          <img src="{{ $pro->avatar }}" class="w-10 h-10 rounded-xl object-cover" alt="">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">{{ $pro->name }}</p>
            <p class="text-xs text-gray-400">{{ $pro->category }} · {{ $pro->city }}</p>
          </div>
          <div class="flex flex-col items-end flex-shrink-0">
            <div class="flex items-center gap-1 text-emerald-500 mb-0.5">
              <i data-lucide="star" class="w-3 h-3 fill-current"></i>
              <span class="text-xs font-bold">{{ $pro->rating ?? '4.8' }}</span>
            </div>
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">{{ $pro->orders ?? 0 }} Orders</p>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Action Bar -->
    <div class="p-6 bg-white border-t border-gray-100 flex-shrink-0">
      <button id="confirm-assign-btn" onclick="confirmAssign()" disabled
        class="w-full py-3.5 rounded-2xl bg-black text-white text-sm font-bold hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
        <span id="confirm-assign-label">Select professionals</span>
        <span id="selected-count" class="hidden px-2 py-0.5 bg-white/20 rounded-lg text-[10px]"></span>
      </button>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .stat-card {
      transition: box-shadow 0.2s;
    }

    .stat-card:hover {
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .table-row {
      transition: background 0.15s;
    }

    .table-row:hover {
      background: #fafafa;
    }

    .drawer-panel.closed {
      transform: translateX(100%);
    }

    .filter-tab {
      transition: all 0.2s;
      border-radius: 0.75rem;
    }

    .filter-tab.active {
      background: #000;
      color: #fff;
    }

    .filter-tab:not(.active) {
      color: #6b7280;
    }

    .filter-tab:not(.active):hover {
      background: #f3f4f6;
      color: #111;
    }

    .page-btn {
      transition: all 0.15s;
    }

    .page-btn.active {
      background: #000;
      color: #fff;
    }

    .page-btn:not(.active):hover {
      background: #f3f4f6;
    }

    .pro-card {
      transition: all 0.2s;
      border: 2px solid transparent;
      cursor: pointer;
    }

    .pro-card:hover {
      border-color: #d1d5db;
      background: #f9fafb;
    }

    .pro-card.selected {
      border-color: #000;
      background: #f9fafb;
    }

    .pro-checkbox {
      width: 20px;
      height: 20px;
      border-radius: 6px;
      border: 2px solid #d1d5db;
      transition: all 0.15s;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .pro-card.selected .pro-checkbox {
      background: #000;
      border-color: #000;
    }

    .pro-card.selected .pro-checkbox svg {
      display: block;
    }

    .pro-card:not(.selected) .pro-checkbox svg {
      display: none;
    }

    /* Auto-assign toggle */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 24px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #e5e7eb;
      transition: .4s;
      border-radius: 34px;
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.toggle-slider {
      background-color: #000;
    }

    input:checked+.toggle-slider:before {
      transform: translateX(20px);
    }

    /* Hide manual buttons when auto-assign is active */
    .auto-assign-active .manual-assign-btn {
      display: none;
    }

    .auto-assign-badge {
      display: none;
    }

    .auto-assign-active .auto-assign-badge {
      display: inline-flex;
    }
  </style>
@endpush

@push('scripts')
  <script>
    const ROWS_PER_PAGE = 8;
    let currentPage = 1, visibleRows = [], currentTab = 'all';

    function setTab(tab) { currentTab = tab; document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active')); document.getElementById('tab-' + tab.toLowerCase().replace(/ /g, '-')).classList.add('active'); applyFilters(); }

    function applyFilters() {
      const search = document.getElementById('assign-search').value.toLowerCase();
      const allRows = Array.from(document.querySelectorAll('#assign-tbody tr.table-row'));
      visibleRows = allRows.filter(row => {
        const textMatch = row.dataset.customer.includes(search) || row.dataset.service.includes(search);
        const tabMatch = currentTab === 'all' || row.dataset.status === currentTab;
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
      btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage === 1, () => { currentPage--; renderPage(); }));
      for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i === currentPage ? 'active border-black' : 'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
      btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage === totalPages || totalPages === 0, () => { currentPage++; renderPage(); }));
    }

    /* Assign Drawer */
    let currentBooking = null, selectedPros = [];
    const statusColors = { 'Unassigned': 'bg-amber-50 text-amber-600', 'Assigned': 'bg-blue-50 text-blue-600', 'In Progress': 'bg-violet-50 text-violet-600', 'Completed': 'bg-emerald-50 text-emerald-600' };

    function openAssignDrawer(booking) {
      if (document.getElementById('auto-assign-toggle').checked && booking.status === 'Unassigned') {
        autoAssignProfessional(booking);
        return;
      }
      currentBooking = booking; selectedPros = [];
      document.getElementById('d-customer-avatar').src = booking.avatar || '';
      document.getElementById('d-customer-name').textContent = booking.displayName || booking.customer || '';
      document.getElementById('d-service-name').textContent = (booking.displayService || booking.service || '') + (booking.package ? ' · ' + booking.package : '');
      document.getElementById('d-booking-date').textContent = (booking.date || '') + (booking.slot ? ' at ' + booking.slot : '') + (booking.city ? ' · ' + booking.city : '');
      document.getElementById('d-booking-label').textContent = `Booking #${booking.id}`;

      const status = booking.status || 'Unassigned';
      const sb = document.getElementById('d-status-badge');
      sb.textContent = status;
      sb.className = 'text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 ' + (statusColors[status] || 'bg-gray-100 text-gray-500');
      document.querySelectorAll('.pro-card').forEach(c => c.classList.remove('selected'));
      document.getElementById('pro-search').value = '';
      filterPros(); updateConfirmBtn();
      document.getElementById('drawer-backdrop').classList.remove('hidden');
      document.getElementById('drawer-panel').classList.remove('closed');
      document.body.style.overflow = 'hidden';
      lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
    }

    function closeAssignDrawer() { document.getElementById('drawer-panel').classList.add('closed'); document.getElementById('drawer-backdrop').classList.add('hidden'); document.body.style.overflow = ''; }

    /* View Booking (DEPRECATED: Now using global view-drawer-btn) */

    function selectPro(el, id, name) {
      const idx = selectedPros.findIndex(p => p.id === id);
      if (idx > -1) { selectedPros.splice(idx, 1); el.classList.remove('selected'); }
      else { selectedPros.push({ id, name }); el.classList.add('selected'); }
      updateConfirmBtn();
    }

    function updateConfirmBtn() {
      const btn = document.getElementById('confirm-assign-btn'), lbl = document.getElementById('confirm-assign-label'), badge = document.getElementById('selected-count');
      const count = selectedPros.length;
      if (count > 0) { btn.disabled = false; badge.textContent = `${count} selected`; badge.classList.remove('hidden'); lbl.textContent = count === 1 ? `Assign ${selectedPros[0].name}` : `Assign ${count} Professionals`; }
      else { btn.disabled = true; lbl.textContent = 'Select professionals'; badge.classList.add('hidden'); }
    }

    function filterPros() { const q = document.getElementById('pro-search').value.toLowerCase(); document.querySelectorAll('.pro-card').forEach(c => c.style.display = c.dataset.proName.includes(q) ? '' : 'none'); }

    function confirmAssign() {
      if (selectedPros.length === 0 || !currentBooking) return;
      const names = selectedPros.map(p => p.name);
      const namesList = names.length === 1 ? `<strong>${names[0]}</strong>` : names.slice(0, -1).map(n => `<strong>${n}</strong>`).join(', ') + ' and <strong>' + names[names.length - 1] + '</strong>';
      const proCount = names.length === 1 ? 'professional' : `${names.length} professionals`;
      Swal.fire({ title: 'Confirm Assignment', html: `Assign ${namesList} to booking <strong>#${currentBooking.id}</strong> for <strong>${currentBooking.customer || currentBooking.customer_name}</strong>?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, Assign ${proCount}` })
        .then(r => { if (r.isConfirmed) { closeAssignDrawer(); const msg = names.length === 1 ? `${names[0]} has been assigned to booking #${currentBooking.id}.` : `${names.length} professionals have been assigned to booking #${currentBooking.id}.`; Swal.fire({ title: 'Assigned!', text: msg, icon: 'success', confirmButtonColor: '#000', timer: 2500, showConfirmButton: false }); } });
    }

    /* Auto Assign logic */
    async function autoAssignProfessional(booking) {
      Swal.fire({
        title: 'Assigning...',
        html: `Automatically assigning best professional to booking <strong>#${booking.id}</strong>...`,
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      try {
        const response = await fetch('{{ route("assign.auto") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ booking_id: booking.id })
        });

        const data = await response.json();

        if (data.success) {
          Swal.fire({
            title: 'Auto-Assigned!',
            html: `<strong>${data.professional_name}</strong> has been successfully assigned to booking <strong>#${booking.id}</strong>.`,
            icon: 'success',
            confirmButtonColor: '#000',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            title: 'Auto-Assign Failed',
            text: data.message || 'No available professionals found.',
            icon: 'warning',
            confirmButtonColor: '#000'
          });
        }
      } catch (error) {
        console.error('Auto-assign error:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to perform auto-assignment. Please try again.',
          icon: 'error',
          confirmButtonColor: '#000'
        });
      }
    }

    function toggleAutoAssign() {
      const isAuto = document.getElementById('auto-assign-toggle').checked;
      localStorage.setItem('auto_assign_enabled', isAuto ? '1' : '0');

      if (isAuto) {
        document.body.classList.add('auto-assign-active');
        Swal.fire({
          title: 'Auto-Assign Enabled',
          text: 'The system will now automatically pick the best professional. Current unassigned bookings will be processed.',
          icon: 'success',
          confirmButtonColor: '#000',
          timer: 2000,
          showConfirmButton: false
        });
        bulkAutoAssignSweep();
      } else {
        document.body.classList.remove('auto-assign-active');
      }
    }

    function bulkAutoAssignSweep() {
      const unassigned = visibleRows.filter(r => r.dataset.status === 'Unassigned');
      if (unassigned.length > 0) {
        // For now, we just suggest the user to refresh or process the first one
        // In a real scenario, we might want to batch this
        console.log('Bulk sweep found %d unassigned bookings', unassigned.length);
      }
    }

    (function init() {
      visibleRows = Array.from(document.querySelectorAll('#assign-tbody tr.table-row'));
      renderPage();

      // Load toggle state
      const autoEnabled = localStorage.getItem('auto_assign_enabled') === '1';
      const toggle = document.getElementById('auto-assign-toggle');
      if (toggle) {
        toggle.checked = autoEnabled;
        if (autoEnabled) document.body.classList.add('auto-assign-active');
      }
    })();
  </script>
@endpush