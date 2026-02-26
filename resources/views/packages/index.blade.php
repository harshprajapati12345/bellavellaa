@extends('layouts.app')
@php $pageTitle = 'Packages'; @endphp

@section('content')
@php
$packages = $packages ?? collect();
$total    = $packages->count();
$active   = $packages->where('status', 'Active')->count();
$inactive = $total - $active;
$topBooked = $packages->sortByDesc('bookings')->first();
$categoryList = $packages->pluck('category')->unique()->filter()->values();
@endphp

    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Packages</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all beauty packages</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="pkg-search" type="text" placeholder="Search packages…" oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
          </div>
          <button onclick="document.getElementById('filter-bar').classList.toggle('hidden')"
            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all sm:hidden">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
          </button>
          <a href="{{ route('packages.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Package
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900">{{ $total }}</p><p class="text-xs text-gray-400 mt-0.5">Packages</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="package" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900">{{ $active }}</p><p class="text-xs text-gray-400 mt-0.5">Live now</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Inactive</p><p class="text-3xl font-bold text-gray-900">{{ $inactive }}</p><p class="text-xs text-gray-400 mt-0.5">Paused</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="pause-circle" class="w-5 h-5 text-gray-400"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Top Booked</p>
            @if($topBooked)<p class="text-lg font-bold text-gray-900 leading-tight mt-0.5">{{ $topBooked->name }}</p><p class="text-xs text-gray-400 mt-0.5">{{ $topBooked->bookings }} bookings</p>@else<p class="text-lg font-bold text-gray-900">–</p>@endif
          </div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-violet-500"></i></div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div id="filter-bar" class="filter-bar bg-white rounded-2xl p-4 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-3">
        <select id="f-category" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Categories</option>
          @foreach($categoryList as $cat)<option value="{{ $cat }}">{{ $cat }}</option>@endforeach
        </select>
        <select id="f-status" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All Statuses</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        <select id="f-price" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">Price</option>
          <option value="asc">Low → High</option>
          <option value="desc">High → Low</option>
        </select>
        <select id="f-duration" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">Duration</option>
          <option value="asc">Short → Long</option>
          <option value="desc">Long → Short</option>
        </select>
        <select id="f-featured" onchange="applyFilters()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-black/5 cursor-pointer">
          <option value="">All</option>
          <option value="1">Featured Only</option>
        </select>
        <button onclick="resetFilters()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-100 transition-all ml-auto">
          <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
        </button>
      </div>

      <!-- Packages Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">

        <!-- Bulk Action Bar -->
        <div id="bulk-bar" class="bulk-bar hidden items-center gap-3 px-5 py-3 bg-gray-900 text-white">
          <span id="bulk-count" class="text-sm font-medium"></span>
          <button onclick="bulkDelete()" class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-all">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Selected
          </button>
          <button onclick="clearSelection()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-medium transition-all">
            <i data-lucide="x" class="w-4 h-4"></i> Cancel
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[1000px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left w-10"><input type="checkbox" id="select-all" onchange="toggleAll(this)" class="w-4 h-4 rounded border-gray-300 cursor-pointer accent-black"></th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Package</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Services</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Duration</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Created</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="pkg-tbody">
              @foreach($packages as $pkg)
              @php
                $finalPrice = $pkg->price - ($pkg->price * (($pkg->discount ?? 0) / 100));
                $hrs = intdiv($pkg->duration ?? 0, 60);
                $mins = ($pkg->duration ?? 0) % 60;
                $durationLabel = ($hrs > 0 ? $hrs.'h ' : '') . ($mins > 0 ? $mins.'m' : '');
                $services = is_array($pkg->services) ? $pkg->services : (is_string($pkg->services) ? json_decode($pkg->services, true) ?? [] : []);
              @endphp
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $pkg->id }}"
                  data-name="{{ strtolower($pkg->name) }}"
                  data-display-name="{{ $pkg->name }}"
                  data-category="{{ $pkg->category }}"
                  data-status="{{ $pkg->status ?? 'Active' }}"
                  data-price="{{ $finalPrice }}"
                  data-original-price="{{ $pkg->price }}"
                  data-duration="{{ $pkg->duration }}"
                  data-duration-label="{{ $durationLabel }}"
                  data-title="{{ $pkg->name }}"
                  data-status="{{ $pkg->status ?? 'Active' }}"
                  data-image="{{ $pkg->image }}"
                  data-bookings="{{ $pkg->bookings ?? 0 }}"
                  data-description="{{ $pkg->description ?? '' }}"
                  data-created="{{ \Carbon\Carbon::parse($pkg->created_at)->format('d M Y') }}">
                <td class="px-5 py-4"><input type="checkbox" class="row-check w-4 h-4 rounded border-gray-300 cursor-pointer accent-black" onchange="updateBulkBar()"></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="{{ $pkg->image ?: 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80' }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">{{ $pkg->name }}</p>
                      @if($pkg->featured)<span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Featured</span>@endif
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ ($pkg->category ?? '') === 'Luxe' ? 'bg-violet-50 text-violet-600' : 'bg-amber-50 text-amber-600' }}">
                    {{ $pkg->category }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex flex-wrap gap-1 max-w-[200px]">
                    @foreach(array_slice($services, 0, 2) as $svc)
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $svc }}</span>
                    @endforeach
                    @if(count($services) > 2)
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">+{{ count($services) - 2 }}</span>
                    @endif
                  </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $durationLabel }}</td>
                <td class="px-5 py-4">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">₹{{ number_format($finalPrice, 0) }}</p>
                    @if(($pkg->discount ?? 0) > 0)
                    <p class="text-xs text-gray-400 line-through">₹{{ number_format($pkg->price, 0) }}</p>
                    @endif
                  </div>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700">{{ $pkg->bookings }}</td>
                <td class="px-5 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" {{ $pkg->status === 'Active' ? 'checked' : '' }} onchange="toggleStatus({{ $pkg->id }}, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td class="px-5 py-4 text-sm text-gray-400">{{ \Carbon\Carbon::parse($pkg->created_at)->format('d M Y') }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                    <button type="button" title="View" data-id="{{ $pkg->id }}"
                      class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                      <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    <a href="{{ route('packages.edit', $pkg->id) }}" title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <button type="button" onclick="deletePackage({{ $pkg->id }}, '{{ addslashes($pkg->name) }}')" title="Delete"
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
            <i data-lucide="package-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No packages found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters</p>
          <button onclick="resetFilters()" class="mt-4 px-5 py-2.5 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all">Reset Filters</button>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>
      </div>

    </div>

    </div>
  </div>
@endsection

@push('styles')
<style>
  .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
  .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
  .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; } .page-btn:not(.active):hover { background: #f3f4f6; }
  .bulk-bar { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
  .filter-bar { transition: all 0.2s; }
</style>
@endpush

@push('scripts')
<script>
  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search   = document.getElementById('pkg-search').value.toLowerCase();
    const category = document.getElementById('f-category').value;
    const status   = document.getElementById('f-status').value;
    const price    = document.getElementById('f-price').value;
    const duration = document.getElementById('f-duration').value;
    const featured = document.getElementById('f-featured').value;
    const allRows  = Array.from(document.querySelectorAll('#pkg-tbody tr.table-row'));

    visibleRows = allRows.filter(row => {
      const nm = row.dataset.name.includes(search);
      const cm = !category || row.dataset.category === category;
      const sm = !status   || row.dataset.status   === status;
      const fm = !featured || row.dataset.featured  === featured;
      return nm && cm && sm && fm;
    });

    if (price === 'asc')    visibleRows.sort((a,b) => +a.dataset.price - +b.dataset.price);
    if (price === 'desc')   visibleRows.sort((a,b) => +b.dataset.price - +a.dataset.price);
    if (duration === 'asc') visibleRows.sort((a,b) => +a.dataset.duration - +b.dataset.duration);
    if (duration === 'desc')visibleRows.sort((a,b) => +b.dataset.duration - +a.dataset.duration);

    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function resetFilters() {
    document.getElementById('pkg-search').value = '';
    ['f-category','f-status','f-price','f-duration','f-featured'].forEach(id => document.getElementById(id).value = '');
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} package${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    const mk = (html, disabled, onClick, extra='') => { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all ${extra}`; b.innerHTML = html; b.disabled = disabled; b.onclick = onClick; return b; };
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>', currentPage===1, () => { currentPage--; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    btns.appendChild(mk('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }, 'disabled:opacity-40 disabled:cursor-not-allowed'));
  }

  // ── Bulk Select ───────────────────────────────────────────────────────
  function toggleAll(cb) { document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked); updateBulkBar(); }
  function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    const bar = document.getElementById('bulk-bar');
    if (checked > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); document.getElementById('bulk-count').textContent = `${checked} selected`; }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); document.getElementById('select-all').checked = false; }
  }
  function clearSelection() { document.querySelectorAll('.row-check, #select-all').forEach(c => c.checked = false); updateBulkBar(); }
  function bulkDelete() {
    const n = document.querySelectorAll('.row-check:checked').length;
    Swal.fire({ title: `Delete ${n} package${n>1?'s':''}?`, text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete' })
      .then(r => { if (r.isConfirmed) { clearSelection(); Swal.fire({ title: 'Deleted!', icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); } });
  }

  // ── Status Toggle ─────────────────────────────────────────────────────
  function toggleStatus(id, el) {
    const newStatus = el.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${newStatus}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes' })
      .then(r => {
        if (!r.isConfirmed) { el.checked = !el.checked; return; }
        fetch(`/packages/${id}/toggle-status`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } });
      });
  }

  // ── Drawer (DEPRECATED: Now using global event delegation in app.js) ──

  (function init() { visibleRows = Array.from(document.querySelectorAll('#pkg-tbody tr.table-row')); renderPage(); })();
</script>
@endpush
