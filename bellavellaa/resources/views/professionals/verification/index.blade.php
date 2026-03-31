@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Document Verification</h2>
            <p class="text-sm text-gray-400 mt-0.5">Review Aadhaar & PAN submissions from professionals</p>
        </div>
        <div class="relative w-full lg:w-72">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="ver-search" type="text" placeholder="Search by name…" oninput="applyFilters()"
                class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-full transition-all">
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Pending Card -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p>
                <p class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Awaiting review</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6 text-amber-500"></i>
            </div>
        </div>
        <!-- Approved Card -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Approved</p>
                <p class="text-3xl font-bold text-gray-900">{{ $approvedCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Verified</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="badge-check" class="w-6 h-6 text-emerald-500"></i>
            </div>
        </div>
        <!-- Rejected Card -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Rejected</p>
                <p class="text-3xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Re-upload needed</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Verification Flow -->
    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-black"></div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Verification Ecosystem</p>
        <div class="flex flex-wrap items-center gap-4">
            @php
            $steps = [
                ['Registers','user-plus','gray'],
                ['Uploads','upload','gray'],
                ['Pending','clock','amber'],
                ['Reviewing','eye','gray'],
                ['Verified','badge-check','emerald'],
                ['Active','check-circle','emerald']
            ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-{{ $step[2] }}-50 border border-{{ $step[2] }}-100">
                    <i data-lucide="{{ $step[1] }}" class="w-3.5 h-3.5 text-{{ $step[2] }}-500"></i>
                    <span class="text-xs font-semibold text-{{ $step[2] }}-700 whitespace-nowrap">{{ $step[0] }}</span>
                </div>
                @if($i < count($steps)-1)
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-200 hidden md:block"></i>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Tabs -->
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2 overflow-x-auto no-scrollbar">
            @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v)
            <button onclick="setTab('{{ $k }}')" id="tab-{{ $k }}"
                class="filter-tab text-xs font-bold px-5 py-2.5 rounded-xl transition-all whitespace-nowrap {{ $k==='all'?'active bg-black text-white':'text-gray-400 hover:bg-gray-50' }}">
                {{ strtoupper($v) }}
            </button>
            @endforeach
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aadhaar No.</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">PAN No.</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Submitted</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody id="ver-tbody">
                    @foreach($requests as $req)
                    <tr class="table-row border-t border-gray-50 hover:bg-gray-50/50 transition-colors"
                        data-id="{{ $req['id'] }}"
                        data-name="{{ strtolower($req['name']) }}"
                        data-status="{{ strtolower($req['status'] ?? 'pending') }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $req['avatar'] }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm" alt="">
                                <p class="text-sm font-semibold text-gray-900">{{ $req['name'] }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $req['aadhaar'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $req['pan'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ date('d M Y', strtotime($req['submitted'])) }}</td>
                        <td class="px-6 py-4">
                            @php
                            $status = $req['status'] ?? 'Pending';
                            $sc = match($status) { 'Approved'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                            $si = match($status) { 'Approved'=>'badge-check', 'Pending'=>'clock', default=>'x-circle' };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $sc }}">
                                <i data-lucide="{{ $si }}" class="w-3.5 h-3.5"></i>{{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('professionals.verification.review', $req['id']) }}" 
                                   class="px-4 py-2 rounded-xl bg-black text-white text-[10px] font-bold uppercase tracking-widest hover:bg-gray-800 transition-all flex items-center gap-2">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 mx-auto border border-gray-100">
                <i data-lucide="search-x" class="w-8 h-8 text-gray-200"></i>
            </div>
            <p class="text-gray-500 font-medium">No verification requests found</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="px-8 py-5 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p id="pagination-info" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"></p>
            <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .filter-tab.active { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>
@endpush

@push('scripts')
<script>
    const ROWS_PER_PAGE = 10;
    let currentPage = 1, visibleRows = [], currentTab = 'all';

    function setTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.filter-tab').forEach(b => {
            b.classList.remove('active', 'bg-black', 'text-white');
            b.classList.add('text-gray-400');
        });
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.classList.add('active', 'bg-black', 'text-white');
        activeBtn.classList.remove('text-gray-400');
        applyFilters();
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
        currentPage = 1; 
        renderPage();
    }

    function renderPage() {
        const start = (currentPage - 1) * ROWS_PER_PAGE, end = start + ROWS_PER_PAGE;
        visibleRows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
        
        const empty = document.getElementById('empty-state'), pw = document.getElementById('pagination-wrap');
        if (visibleRows.length === 0) {
            empty.classList.replace('hidden', 'flex');
            pw.classList.add('hidden');
        } else {
            empty.classList.replace('flex', 'hidden');
            pw.classList.remove('hidden');
        }
        renderPagination();
    }

    function renderPagination() {
        const total = visibleRows.length, totalPages = Math.ceil(total / ROWS_PER_PAGE);
        const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total), end = Math.min(currentPage * ROWS_PER_PAGE, total);
        document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} entries`;
        
        const btns = document.getElementById('pagination-btns'); 
        btns.innerHTML = '';
        
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const b = document.createElement('button');
            b.className = `w-8 h-8 rounded-lg text-xs font-bold transition-all border ${i===currentPage?'bg-black text-white border-black':'text-gray-400 bg-white border-gray-100 hover:border-gray-300'}`;
            b.textContent = i;
            b.onclick = () => { currentPage = i; renderPage(); };
            btns.appendChild(b);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        visibleRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row'));
        renderPage();
    });
</script>
@endpush
