@extends('layouts.app')
@php $pageTitle = 'Services'; @endphp

@section('content')
@php
$services = $services ?? [];
$totalServices   = count($services);
$activeServices  = $services instanceof \Illuminate\Support\Collection ? $services->where('status', 'Active')->count() : 0;
$inactiveServices = $totalServices - $activeServices;
$mostBooked = $services instanceof \Illuminate\Support\Collection ? $services->sortByDesc('bookings')->first() : null;

$categoryList = $services instanceof \Illuminate\Support\Collection ? $services->pluck('category')->unique()->filter()->values() : collect();
@endphp

    <div class="flex flex-col gap-6">

      <!-- ── Page Header ─────────────────────────────────────────────────── -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Services</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all beauty services</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <!-- Search -->
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="svc-search" type="text" placeholder="Search services…" oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
          </div>
          <!-- Filter toggle (mobile) -->
          <button onclick="document.getElementById('filter-bar').classList.toggle('hidden')"
            class="sm:hidden flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
          </button>
          <!-- Add Service -->
          <a href="{{ route('services.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Service
          </a>
        </div>
      </div>

      <!-- ── Stat Cards ──────────────────────────────────────────────────── -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalServices }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Services</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="layers" class="w-5 h-5 text-gray-600"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p>
            <p class="text-3xl font-bold text-gray-900">{{ $activeServices }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Live now</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Inactive</p>
            <p class="text-3xl font-bold text-gray-900">{{ $inactiveServices }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Paused</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="pause-circle" class="w-5 h-5 text-gray-400"></i>
          </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Top Booked</p>
            <p class="text-base font-bold text-gray-900 leading-tight">{{ $mostBooked->name ?? 'N/A' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $mostBooked->bookings ?? 0 }} bookings</p>
          </div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-amber-500"></i>
          </div>
        </div>
      </div>

      <!-- ── Filter Bar ──────────────────────────────────────────────────── -->
      <div id="filter-bar" class="bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest mr-1">Filters</span>

        <select id="f-category" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">All Categories</option>
          @foreach($categoryList as $cat)
          <option value="{{ $cat }}">{{ $cat }}</option>
          @endforeach
        </select>

        <select id="f-status" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <select id="f-price" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">Price Sort</option>
          <option value="asc">Price: Low → High</option>
          <option value="desc">Price: High → Low</option>
        </select>

        <select id="f-duration" onchange="applyFilters()"
          class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer">
          <option value="">Duration Sort</option>
          <option value="asc">Duration: Short → Long</option>
          <option value="desc">Duration: Long → Short</option>
        </select>

        <button onclick="resetFilters()"
          class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-black px-3 py-2 rounded-xl hover:bg-gray-100 transition-all ml-auto">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
        </button>
      </div>

      <!-- ── Bulk Action Bar (hidden by default) ─────────────────────────── -->
      <div id="bulk-bar" class="hidden bg-black text-white rounded-2xl px-5 py-3 flex items-center gap-4">
        <span id="bulk-count" class="text-sm font-medium"></span>
        <div class="flex-1"></div>
        <button onclick="bulkDelete()" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-all">
          <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Selected
        </button>
        <button onclick="clearSelection()" class="text-gray-400 hover:text-white transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- ── Services Table ──────────────────────────────────────────────── -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left w-10">
                  <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                    class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer">
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Duration</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Created</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="services-tbody">
              @foreach($services as $svc)
              @php $catClass = 'badge-' . strtolower($svc->category ?? 'default'); @endphp
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $svc->id }}"
                  data-name="{{ strtolower($svc->name) }}"
                  data-display-name="{{ $svc->name }}"
                  data-category="{{ $svc->category }}"
                  data-title="{{ $svc->name }}"
                  data-status="{{ $svc->status ?? 'Active' }}"
                  data-price="{{ $svc->price }}"
                  data-duration="{{ $svc->duration }}"
                  data-bookings="{{ $svc->bookings ?? 0 }}"
                  data-description="{{ $svc->description ?? '' }}"
                  data-created="{{ \Carbon\Carbon::parse($svc->created_at)->format('d M Y') }}"
                  data-featured="{{ $svc->featured ? 'true' : 'false' }}"
                  data-image="{{ $svc->image }}">

                <td class="px-5 py-4">
                  <input type="checkbox" class="row-check w-4 h-4 rounded border-gray-300 accent-black cursor-pointer" onchange="onRowCheck()">
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                      <img src="{{ $svc->image ? asset('storage/'.$svc->image) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $svc->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $svc->name }}</p>
                      @if($svc->featured)
                      <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full mt-0.5">
                        <i data-lucide="star" class="w-2.5 h-2.5 fill-current"></i> Featured
                      </span>
                      @endif
                    </div>
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $catClass }}">
                    {{ $svc->category }}
                  </span>
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5 text-sm text-gray-600">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                    {{ $svc->duration }} mins
                  </div>
                </td>

                <td class="px-5 py-4">
                  <span class="text-sm font-semibold text-gray-900">₹{{ number_format($svc->price) }}</span>
                </td>

                <td class="px-5 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" {{ $svc->status === 'Active' ? 'checked' : '' }}
                      onchange="toggleStatus({{ $svc->id }}, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>

                <td class="px-5 py-4">
                  <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($svc->created_at)->format('d M Y') }}</span>
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <button type="button" title="View" data-id="{{ $svc->id }}"
                      class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    <a href="{{ route('services.edit', $svc->id) }}" title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <button type="button" onclick="deleteService({{ $svc->id }}, '{{ addslashes($svc->name) }}')" title="Delete"
                      class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                      <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <i data-lucide="search-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No services found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters</p>
          <button onclick="resetFilters()" class="mt-4 px-5 py-2 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all">
            Reset Filters
          </button>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>
      </div>

    </div><!-- /flex-col gap-6 -->

@endsection

@push('styles')
<style>
  .table-row.selected { background: #f0f9ff; }
  .drawer-panel.closed { transform: translateX(100%); }
  .page-btn { transition: all 0.15s; }
  .page-btn.active { background: #000; color: #fff; }
  .page-btn:not(.active):hover { background: #f3f4f6; }
  .badge-luxe { background: #f5f3ff; color: #7c3aed; }
  .badge-prime { background: #fff7ed; color: #c2410c; }
  .badge-bridal { background: #fdf2f8; color: #be185d; }
  .badge-grooming { background: #f0fdf4; color: #15803d; }
  .badge-spa { background: #eff6ff; color: #1d4ed8; }
  .badge-skin { background: #fefce8; color: #a16207; }
  #bulk-bar { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
</style>
@endpush

@push('scripts')
<script>
  // ── Pagination state ─────────────────────────────────────────────────────
  const ROWS_PER_PAGE = 5;
  let currentPage = 1;
  let visibleRows = [];

  // ── Filter & Search ──────────────────────────────────────────────────────
  function applyFilters() {
    const search   = document.getElementById('svc-search').value.toLowerCase();
    const category = document.getElementById('f-category').value;
    const status   = document.getElementById('f-status').value;
    const priceSort = document.getElementById('f-price').value;
    const durSort  = document.getElementById('f-duration').value;

    const allRows = Array.from(document.querySelectorAll('#services-tbody tr.table-row'));

    visibleRows = allRows.filter(row => {
      const nameMatch = row.dataset.name.includes(search);
      const catMatch  = !category || row.dataset.category === category;
      const statMatch = !status   || row.dataset.status === status;
      return nameMatch && catMatch && statMatch;
    });

    if (priceSort) {
      visibleRows.sort((a, b) => priceSort === 'asc'
        ? a.dataset.price - b.dataset.price
        : b.dataset.price - a.dataset.price);
    } else if (durSort) {
      visibleRows.sort((a, b) => durSort === 'asc'
        ? a.dataset.duration - b.dataset.duration
        : b.dataset.duration - a.dataset.duration);
    }

    allRows.forEach(r => { r.style.display = 'none'; r.style.order = ''; });
    visibleRows.forEach((r, i) => { r.style.order = i; });

    currentPage = 1;
    renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end   = start + ROWS_PER_PAGE;

    visibleRows.forEach((r, i) => {
      r.style.display = (i >= start && i < end) ? '' : 'none';
    });

    const empty = document.getElementById('empty-state');
    const paginationWrap = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) {
      empty.classList.remove('hidden');
      empty.classList.add('flex');
      paginationWrap.classList.add('hidden');
    } else {
      empty.classList.add('hidden');
      empty.classList.remove('flex');
      paginationWrap.classList.remove('hidden');
    }

    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length;
    const totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total);
    const end   = Math.min(currentPage * ROWS_PER_PAGE, total);

    document.getElementById('pagination-info').textContent =
      `Showing ${start}–${end} of ${total} service${total !== 1 ? 's' : ''}`;

    const btns = document.getElementById('pagination-btns');
    btns.innerHTML = '';

    const prev = document.createElement('button');
    prev.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
    prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>';
    prev.disabled = currentPage === 1;
    prev.onclick = () => { currentPage--; renderPage(); };
    btns.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement('button');
      btn.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i === currentPage ? 'active border-black' : 'border-gray-200 text-gray-600'}`;
      btn.textContent = i;
      btn.onclick = () => { currentPage = i; renderPage(); };
      btns.appendChild(btn);
    }

    const next = document.createElement('button');
    next.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
    next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
    next.disabled = currentPage === totalPages;
    next.onclick = () => { currentPage++; renderPage(); };
    btns.appendChild(next);
  }

  function resetFilters() {
    document.getElementById('svc-search').value = '';
    document.getElementById('f-category').value = '';
    document.getElementById('f-status').value = '';
    document.getElementById('f-price').value = '';
    document.getElementById('f-duration').value = '';
    applyFilters();
  }

  // ── Bulk Selection ───────────────────────────────────────────────────────
  function toggleSelectAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    onRowCheck();
  }

  function onRowCheck() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulk-bar');
    if (checked.length > 0) {
      bar.classList.remove('hidden');
      bar.classList.add('flex');
      document.getElementById('bulk-count').textContent = `${checked.length} service${checked.length > 1 ? 's' : ''} selected`;
    } else {
      bar.classList.add('hidden');
      bar.classList.remove('flex');
    }
    const all = document.querySelectorAll('.row-check');
    document.getElementById('select-all').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('select-all').checked = checked.length === all.length;
  }

  function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    onRowCheck();
  }

  function bulkDelete() {
    const count = document.querySelectorAll('.row-check:checked').length;
    Swal.fire({
      title: `Delete ${count} service${count > 1 ? 's' : ''}?`,
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete',
    }).then(r => {
      if (r.isConfirmed) {
        Swal.fire({ title: 'Deleted!', text: `${count} service${count > 1 ? 's' : ''} removed.`, icon: 'success', confirmButtonColor: '#000', timer: 2000, showConfirmButton: false });
        clearSelection();
      }
    });
  }

  // ── Toggle Status ────────────────────────────────────────────────────────
  function toggleStatus(id, checkbox) {
    const newStatus = checkbox.checked ? 'Active' : 'Inactive';
    Swal.fire({
      title: `Set to ${newStatus}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#000',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: `Yes, ${newStatus}`,
    }).then(r => {
      if (!r.isConfirmed) { checkbox.checked = !checkbox.checked; return; }
      fetch(`/services/${id}/toggle-status`, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      }).then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      }).then(() => {
        Swal.fire({ title: 'Updated!', text: `Service is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
      }).catch(err => {
        checkbox.checked = !checkbox.checked;
        Swal.fire({ title: 'Error', text: 'Failed to update status.', icon: 'error', confirmButtonColor: '#000' });
      });
    });
  }

  // ── Drawer (DEPRECATED: Now using global event delegation in app.js) ──

  function deleteService(id, name) {
    Swal.fire({
      title: `Delete ${name}?`,
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete'
    }).then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/services/${id}`;
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // ── Init ─────────────────────────────────────────────────────────────────
  (function init() {
    visibleRows = Array.from(document.querySelectorAll('#services-tbody tr.table-row'));
    renderPage();
  })();
</script>
@endpush
