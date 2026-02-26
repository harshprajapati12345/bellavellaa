@extends('layouts.app')
@php $pageTitle = 'Categories'; @endphp

@section('content')
@php
$categories = $categories ?? [];
// Counts are passed from the controller: $totalCats, $totalSvcs, $totalBookings, $totalActive, $topCategory
@endphp

    <div class="flex flex-col gap-6">

      <!-- ── Page Header ──────────────────────────────────────────────────── -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Service Categories</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all service categories in one place</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ route('categories.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
          </a>
        </div>
      </div>

      <!-- ── Stat Cards ───────────────────────────────────────────────────── -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900">{{ $totalCats }}</p><p class="text-xs text-gray-400 mt-0.5">Categories</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="folder" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Active</p><p class="text-3xl font-bold text-gray-900">{{ $totalActive }}</p><p class="text-xs text-gray-400 mt-0.5">Live categories</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Services</p><p class="text-3xl font-bold text-gray-900">{{ $totalSvcs }}</p><p class="text-xs text-gray-400 mt-0.5">Active services</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="store" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-violet-500 uppercase tracking-widest mb-1">Top Performing</p><p class="text-lg font-bold text-gray-900 mt-1">{{ $topCategory->name ?? 'N/A' }}</p><p class="text-xs text-gray-400 mt-0.5">{{ $topCategory ? number_format($topCategory->bookings_count) : 0 }} bookings</p></div>
          <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0"><i data-lucide="trending-up" class="w-5 h-5 text-violet-500"></i></div>
        </div>
      </div>

      <!-- ── Table Layout ─────────────────────────────────────────────── -->
      <div class="flex flex-col gap-4">

        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="relative">
              <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
              <input id="cat-search" type="text" placeholder="Search categories…" oninput="applyFilters()"
                class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
            </div>
            <select id="f-status" onchange="applyFilters()"
              class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 cursor-pointer transition-all">
              <option value="">All Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Services</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Bookings</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                  <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
              </thead>
              <tbody id="categories-tbody">
                @foreach($categories as $cat)
                <tr class="table-row border-b border-gray-50"
                    data-id="{{ $cat->id }}"
                    data-name="{{ strtolower($cat->name) }}"
                    data-title="{{ $cat->name }}"
                    data-status="{{ $cat->status ?? 'Active' }}"
                    data-slug="{{ $cat->slug ?? Str::slug($cat->name) }}"
                    data-image="{{ $cat->image }}"
                    data-services="{{ $cat->services_count ?? 0 }}"
                    data-bookings="{{ $cat->bookings_count ?? 0 }}"
                    data-description="{{ $cat->description }}"
                    data-created="{{ \Carbon\Carbon::parse($cat->created_at)->format('d M Y') }}">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="{{ $cat->image ? asset('storage/'.$cat->image) : 'https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=400&q=80' }}" class="w-full h-full object-cover" alt="">
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $cat->name }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">/{{ $cat->slug ?? Str::slug($cat->name) }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm font-medium text-gray-900">{{ $cat->services_count ?? 0 }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm font-medium text-gray-900">{{ number_format($cat->bookings_count ?? 0) }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <label class="toggle-switch">
                      <input type="checkbox" {{ ($cat->status ?? 'Active') === 'Active' ? 'checked' : '' }} onchange="toggleStatus({{ $cat->id }}, this)">
                      <span class="toggle-slider"></span>
                    </label>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                      <button type="button" title="View" data-id="{{ $cat->id }}"
                        class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      </button>
                      <a href="{{ route('categories.edit', $cat->id) }}" title="Edit"
                        class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                      </a>
                      <button type="button" title="Delete" onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
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

          <!-- Empty state -->
          <div id="empty-state" class="hidden flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
              <i data-lucide="folder-x" class="w-7 h-7 text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium">No categories found</p>
            <p class="text-gray-400 text-sm mt-1">Try adjusting your filter</p>
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
  </div>
@endsection

@push('styles')
<style>
  .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
  .page-btn:not(.active):hover { background: #f3f4f6; }
</style>
@endpush

@push('scripts')
<script>
  // ── Pagination & Filters ───────────────────────────────────────────────────
  const ROWS_PER_PAGE = 8;
  let currentPage = 1, visibleRows = [];

  function applyFilters() {
    const search = document.getElementById('cat-search').value.toLowerCase();
    const status = document.getElementById('f-status').value;
    const allRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row'));
    
    visibleRows = allRows.filter(row => {
      const nameMatch   = row.dataset.name.includes(search);
      const statusMatch = !status || row.dataset.status === status;
      return nameMatch && statusMatch;
    });
    
    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE, end = start + ROWS_PER_PAGE;
    visibleRows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
    
    const empty = document.getElementById('empty-state'), pw = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) {
      empty.classList.remove('hidden'); empty.classList.add('flex');
      pw.classList.add('hidden');
    } else {
      empty.classList.add('hidden'); empty.classList.remove('flex');
      pw.classList.remove('hidden');
    }
    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length, totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total), end = Math.min(currentPage * ROWS_PER_PAGE, total);
    
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} categor${total !== 1 ? 'ies' : 'y'}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    
    const mkArrow = (svg, disabled, onClick) => {
        const b = document.createElement('button');
        b.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed';
        b.innerHTML = svg; b.disabled = disabled; b.onclick = onClick;
        return b;
    };
    
    btns.appendChild(mkArrow('<i data-lucide="chevron-left" class="w-4 h-4"></i>', currentPage===1, () => { currentPage--; renderPage(); }));
    for (let i = 1; i <= totalPages; i++) {
        const b = document.createElement('button');
        b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`;
        b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); };
        btns.appendChild(b);
    }
    btns.appendChild(mkArrow('<i data-lucide="chevron-right" class="w-4 h-4"></i>', currentPage===totalPages||totalPages===0, () => { currentPage++; renderPage(); }));
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  // ── Toggle Status ─────────────────────────────────────────────────────────
  function toggleStatus(id, checkbox) {
    const newStatus = checkbox.checked ? 'Active' : 'Inactive';
    Swal.fire({ title: `Set to ${newStatus}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, ${newStatus}` })
      .then(r => {
        if (!r.isConfirmed) { checkbox.checked = !checkbox.checked; return; }
        fetch(`/categories/${id}/toggle-status`, {
          method: 'PATCH',
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
        }).then(() => {
          Swal.fire({ title: 'Updated!', text: `Category is now ${newStatus}.`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false });
        });
      });
  }

  // ── Drawer (DEPRECATED: Now using global event delegation in app.js) ──

  // ── Init ──────────────────────────────────────────────────────────────────
  (function init() {
    visibleRows = Array.from(document.querySelectorAll('#categories-tbody tr.table-row'));
    renderPage();
  })();
</script>
@endpush
