@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Document Verification</h2>
          <p class="text-sm text-gray-400 mt-0.5">Review Aadhaar & PAN submissions from professionals</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="ver-search" type="text" placeholder="Search by name…" oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p>
            <p class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Awaiting review</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="clock" class="w-6 h-6 text-amber-500"></i>
          </div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Approved</p>
            <p class="text-3xl font-bold text-gray-900">{{ $approvedCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Verified</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="badge-check" class="w-6 h-6 text-emerald-500"></i>
          </div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Rejected</p>
            <p class="text-3xl font-bold text-gray-900">{{ $rejectedCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Re-upload needed</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="x-circle" class="w-6 h-6 text-red-400"></i>
          </div>
        </div>
      </div>

      <!-- Verification Flow -->
      <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-black"></div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Verification Ecosystem</p>
        <div class="flex flex-wrap items-center gap-3">
          @php
          $steps = [
            ['Registers','user-plus','gray'],
            ['Uploads Docs','upload','gray'],
            ['Pending Review','clock','amber'],
            ['Admin Reviews','eye','gray'],
            ['Approved','badge-check','emerald'],
            ['Active Professional','check-circle','emerald']
          ];
          @endphp
          @foreach($steps as $i => $step)
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-{{ $step[2] }}-50/50 border border-{{ $step[2] }}-100/50">
              <i data-lucide="{{ $step[1] }}" class="w-3.5 h-3.5 text-{{ $step[2] }}-500"></i>
              <span class="text-xs font-semibold text-{{ $step[2] }}-700 whitespace-nowrap">{{ $step[0] }}</span>
            </div>
            @if($i < count($steps)-1)
              <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-gray-200"></i>
            @endif
          </div>
          @endforeach
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white rounded-[2rem] shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        
        <!-- Filter Tabs -->
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-1.5 overflow-x-auto">
          @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v)
          <button onclick="setTab('{{ $k }}')" id="tab-{{ $k }}"
            class="filter-tab text-xs font-semibold px-5 py-2 {{ $k==='all'?'active':'' }} whitespace-nowrap tracking-wide">
            {{ strtoupper($v) }}
          </button>
          @endforeach
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px]">
            <thead>
              <tr class="border-b border-gray-50 bg-gray-50/30">
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Professional</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Aadhaar No.</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">PAN No.</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Submitted</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Status</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Action</th>
              </tr>
            </thead>
            <tbody id="ver-tbody">
              @foreach($requests as $req)
              <tr class="table-row border-b border-gray-50/50"
                  data-id="{{ $req['id'] }}"
                  data-name="{{ $req['name'] }}"
                  data-avatar="{{ $req['avatar'] }}"
                  data-status="{{ strtolower($req['status']) }}"
                  data-aadhaar="{{ $req['aadhaar'] }}"
                  data-pan="{{ $req['pan'] }}"
                  data-submitted="{{ date('d M Y', strtotime($req['submitted'])) }}">
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <img src="{{ $req['avatar'] }}" class="w-10 h-10 rounded-full object-cover avatar-ring flex-shrink-0" alt="">
                    <p class="text-sm font-semibold text-gray-900">{{ $req['name'] }}</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-sm text-gray-600 font-mono tracking-tight">{{ $req['aadhaar'] }}</td>
                <td class="px-6 py-5 text-sm text-gray-600 font-mono tracking-tight">{{ $req['pan'] }}</td>
                <td class="px-6 py-5">
                  <p class="text-sm text-gray-500">{{ date('d M Y', strtotime($req['submitted'])) }}</p>
                </td>
                <td class="px-6 py-5">
                  @php
                  $sc = match($req['status']) { 'Approved'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                  $si = match($req['status']) { 'Approved'=>'badge-check', 'Pending'=>'clock', default=>'x-circle' };
                  @endphp
                  <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $sc }}">
                    <i data-lucide="{{ $si }}" class="w-3 h-3"></i>{{ $req['status'] }}
                  </span>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button onclick="openDrawer(this.closest('tr').dataset)" title="View Details"
                      class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:text-black hover:bg-gray-100 transition-all border border-gray-100">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    @if($req['status'] === 'Pending')
                    <a href="{{ route('professionals.verification.review', $req['id']) }}" title="Review Submission"
                      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-all">
                      <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Review
                    </a>
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
          <div class="w-16 h-16 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-4 mx-auto border border-gray-100">
            <i data-lucide="search-x" class="w-8 h-8 text-gray-200"></i>
          </div>
          <p class="text-gray-500 font-medium">No verification requests found</p>
          <p class="text-gray-400 text-xs mt-1">Try a different search or filter</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-8 py-5 border-t border-gray-50 bg-gray-50/20">
          <p id="pagination-info" class="text-xs font-medium text-gray-400 uppercase tracking-widest"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>

      </div>

    </div>
    </div>

<!-- ── VIEW DRAWER ─────────────────────────────────────────────────────────── -->
<div id="drawer-backdrop" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm" onclick="closeDrawer()"></div>
<div id="drawer-panel" class="drawer-panel closed fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col overflow-hidden">
  
  <div class="relative h-44 flex-shrink-0">
    <img id="d-avatar" src="" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
    <button onclick="closeDrawer()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 backdrop-blur-md hover:bg-white/40 flex items-center justify-center transition-all">
      <i data-lucide="x" class="w-4 h-4 text-white"></i>
    </button>
    <div class="absolute bottom-4 left-6 right-6">
      <h3 id="d-name" class="text-xl font-bold text-white mb-1"></h3>
      <div class="flex items-center gap-2">
        <span id="d-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"></span>
        <span id="d-submitted" class="text-xs text-white/70"></span>
      </div>
    </div>
  </div>

  <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
    <!-- ID Documents -->
    <div>
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 text-center">Identity Documents</p>
      <div class="space-y-4">
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-200 flex items-center justify-center"><i data-lucide="credit-card" class="w-5 h-5 text-gray-600"></i></div>
            <div>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Aadhaar Card</p>
              <p id="d-aadhaar" class="text-sm font-bold text-gray-900 font-mono"></p>
            </div>
          </div>
          <button class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-black transition-colors" title="View Document Image"><i data-lucide="external-link" class="w-4 h-4"></i></button>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-200 flex items-center justify-center"><i data-lucide="file-text" class="w-5 h-5 text-gray-600"></i></div>
            <div>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">PAN Card</p>
              <p id="d-pan" class="text-sm font-bold text-gray-900 font-mono"></p>
            </div>
          </div>
          <button class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-black transition-colors" title="View Document Image"><i data-lucide="external-link" class="w-4 h-4"></i></button>
        </div>
      </div>
    </div>

    <!-- Verification Disclaimer -->
    <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100">
      <div class="flex items-center gap-3 mb-2">
        <i data-lucide="shield-alert" class="w-5 h-5 text-amber-500"></i>
        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Admin Responsibility</p>
      </div>
      <p class="text-[11px] text-amber-600 leading-relaxed italic">By approving this professional, you confirm that you have manually verified the physical documents against the provided information. This action will grant them an "Active" status on the platform.</p>
    </div>
  </div>

  <div class="flex items-center gap-3 px-6 py-5 border-t border-gray-100 flex-shrink-0">
    <button id="d-approve-btn" class="hidden flex-1 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
      <i data-lucide="check" class="w-4 h-4"></i> Approve & Verify
    </button>
    <button id="d-reject-btn" class="hidden w-12 h-12 rounded-xl border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center">
      <i data-lucide="x" class="w-4 h-4"></i>
    </button>
  </div>
</div>

@push('styles')
<style>
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .avatar-ring { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #e5e7eb; }
</style>
@endpush

@push('scripts')
<script>
  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [], currentTab = 'all';

  function setTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    applyFilters();
  }

  /* View Drawer */
  function openDrawer(r) {
    document.getElementById('d-name').textContent = r.name;
    document.getElementById('d-avatar').src = r.avatar;
    document.getElementById('d-aadhaar').textContent = r.aadhaar;
    document.getElementById('d-pan').textContent = r.pan;
    document.getElementById('d-submitted').textContent = 'Submitted on ' + r.submitted;

    const sb = document.getElementById('d-status-badge');
    sb.textContent = r.status.toUpperCase();
    const sc = { approved: 'bg-emerald-100 text-emerald-700', pending: 'bg-amber-100 text-amber-700', rejected: 'bg-red-100 text-red-700' };
    sb.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ' + (sc[r.status] || 'bg-gray-100 text-gray-700');

    const approveBtn = document.getElementById('d-approve-btn');
    const rejectBtn = document.getElementById('d-reject-btn');

    if (r.status === 'pending') {
      approveBtn.classList.remove('hidden');
      rejectBtn.classList.remove('hidden');
    } else {
      approveBtn.classList.add('hidden');
      rejectBtn.classList.add('hidden');
    }

    document.getElementById('drawer-backdrop').classList.remove('hidden');
    document.getElementById('drawer-panel').classList.remove('closed');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
  }

  function closeDrawer() {
    document.getElementById('drawer-panel').classList.add('closed');
    document.getElementById('drawer-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
  }

  function applyFilters() {
    const search = document.getElementById('ver-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const nameMatch = row.dataset.name.includes(search);
      const tabMatch = currentTab === 'all' || row.dataset.status === currentTab;
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
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} request${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    
    // Prev
    const prev = document.createElement('button');
    prev.className = 'page-btn w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-white transition-all';
    prev.innerHTML = '<i data-lucide="chevron-left" class="w-4 h-4"></i>';
    prev.disabled = currentPage === 1; 
    prev.onclick = () => { currentPage--; renderPage(); }; btns.appendChild(prev);
    
    for (let i = 1; i <= totalPages; i++) {
        const b = document.createElement('button');
        b.className = `page-btn w-9 h-9 rounded-xl text-xs font-bold transition-all border ${i===currentPage?'active border-black':'border-gray-200 text-gray-400 bg-white hover:border-gray-400'}`;
        b.textContent = i;
        b.onclick = () => { currentPage = i; renderPage(); };
        btns.appendChild(b);
    }
    
    // Next
    const next = document.createElement('button');
    next.className = 'page-btn w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-white transition-all';
    next.innerHTML = '<i data-lucide="chevron-right" class="w-4 h-4"></i>';
    next.disabled = currentPage === totalPages || totalPages === 0;
    next.onclick = () => { currentPage++; renderPage(); }; btns.appendChild(next);
    
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row')); renderPage(); })();
</script>
@endpush
