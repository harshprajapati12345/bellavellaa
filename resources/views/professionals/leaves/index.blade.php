@extends('layouts.app')

@section('title', 'Leave Requests · Bellavella Admin')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Leave Requests</h2>
            <p class="text-sm text-gray-400 mt-1">Manage and track professional leave submissions</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-white text-gray-900 border border-gray-200 px-5 py-2.5 rounded-xl font-medium text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-50 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pending Requests</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $pendingCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-50 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Approved This Month</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $approvedMonth }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-50 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">On Leave Today</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $onLeaveToday }}</p>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <!-- Quick Filters & Toolbar -->
        <div class="p-6 border-b border-gray-50 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="setQuickFilter('')" class="q-filter active px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-gray-100" data-val="">All Requests</button>
                <button onclick="setQuickFilter('Pending')" class="q-filter px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-gray-100" data-val="Pending">Pending</button>
                <button onclick="setQuickFilter('Approved')" class="q-filter px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-gray-100" data-val="Approved">Approved</button>
                <button onclick="setQuickFilter('On Leave')" class="q-filter px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-gray-100" data-val="On Leave">On Leave Today</button>
                <button onclick="setQuickFilter('Rejected')" class="q-filter px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-gray-100" data-val="Rejected">Rejected</button>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <div class="relative flex-1 group">
                     <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-black transition-all pointer-events-none"></i>
                     <input type="text" id="search-input" onkeyup="applyFilters()" placeholder="Search professional or EMP ID…" class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-black/5 outline-none transition-all">
                </div>
                <div class="flex items-center gap-3">
                    <select id="filter-status" onchange="syncPills(); applyFilters();" class="bg-gray-50 border-none rounded-2xl text-sm px-5 py-3 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="On Leave">On Leave Today</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Type</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Dates</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Days</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Reason</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="leave-table-body" class="divide-y divide-gray-50">
                    @forelse($requests as $leave)
                    @php
                        $startDate = \Carbon\Carbon::parse($leave->start_date);
                        $endDate = \Carbon\Carbon::parse($leave->end_date);
                        $days = $startDate->diffInDays($endDate) + 1;
                        $isOnLeave = now()->between($startDate->startOfDay(), $endDate->endOfDay()) && $leave->status === 'Approved';
                    @endphp
                    <tr class="leave-row hover:bg-gray-50/80 transition-all group" 
                        data-name="{{ strtolower($leave->professional->name) }}" 
                        data-status="{{ $leave->status }}"
                        data-onleave="{{ $isOnLeave ? 'true' : 'false' }}">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-400 text-xs">
                                    {{ substr($leave->professional->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $leave->professional->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">ID: {{ $leave->professional_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-medium text-gray-600">{{ $leave->professional->category }}</span>
                        </td>
                        <td class="px-6 py-5 text-center px-6">
                            <span class="text-xs font-semibold text-gray-900">{{ $leave->leave_type ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-gray-900">{{ $startDate->format('d M') }} - {{ $endDate->format('d M') }}</span>
                                <span class="text-[9px] text-gray-400 uppercase tracking-tighter">{{ $startDate->format('Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-sm font-bold text-gray-900">{{ $days }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs text-gray-400 line-clamp-1 max-w-[150px]">{{ $leave->reason }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="badge status-{{ strtolower($leave->status) }}">{{ $leave->status }}</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="viewDetails({{ json_encode([
                                    'id' => $leave->id,
                                    'pro_name' => $leave->professional->name,
                                    'empid' => $leave->professional_id,
                                    'category' => $leave->professional->category,
                                    'type' => $leave->leave_type ?? 'N/A',
                                    'start_date' => $startDate->format('d M Y'),
                                    'end_date' => $endDate->format('d M Y'),
                                    'days' => $days,
                                    'reason' => $leave->reason,
                                    'status' => $leave->status,
                                    'submitted_at' => $leave->created_at->format('d M Y, h:i A')
                                ]) }})" class="p-2 bg-gray-50 text-gray-400 hover:text-black hover:bg-gray-100 rounded-xl transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                @if($leave->status === 'Pending')
                                <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" class="inline" id="approve-form-{{ $leave->id }}">
                                    @csrf
                                    <button type="button" onclick="confirmApprove({{ $leave->id }}, '{{ addslashes($leave->professional->name) }}')" class="p-2 bg-emerald-50 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100 rounded-xl transition-all">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                <form action="{{ route('leaves.reject', $leave->id) }}" method="POST" class="inline" id="reject-form-{{ $leave->id }}">
                                    @csrf
                                    <button type="button" onclick="confirmReject({{ $leave->id }}, '{{ addslashes($leave->professional->name) }}')" class="p-2 bg-red-50 text-red-400 hover:text-red-700 hover:bg-red-100 rounded-xl transition-all">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No leave requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="no-results" class="hidden py-16 text-center">
                <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                <p class="text-sm text-gray-400">No results match your filters.</p>
            </div>
        </div>
    </div>
</div>

<!-- Detail Drawer Overlay -->
<div id="drawer-overlay" class="fixed inset-0 z-50 drawer-overlay hidden flex justify-end" onclick="closeDrawer(event)">
    <div id="drawer-content" class="w-full max-w-lg bg-white h-full shadow-2xl drawer-content translate-x-full overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Drawer Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Leave Details</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="d-id">LR-0000</p>
            </div>
            <button onclick="closeDrawer()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-all">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>

        <!-- Drawer Body -->
        <div class="p-8 space-y-8">
            <!-- Professional Info -->
            <div class="flex items-center gap-4 bg-gray-50 rounded-3xl p-6">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center font-bold text-xl text-gray-900" id="d-avatar">
                    P
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900" id="d-name">Priya Sharma</p>
                    <p class="text-sm text-gray-400" id="d-empid">BV-PRO-001</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full" id="d-category">Salon</span>
                    </div>
                </div>
            </div>

            <!-- Leave Metadata -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white border border-gray-100 rounded-3xl p-5">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Leave Type</p>
                    <p class="text-sm font-bold text-gray-900" id="d-type">Sick Leave</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-3xl p-5 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Total Days</p>
                    <p class="text-sm font-bold text-gray-900" id="d-days">3 Days</p>
                </div>
            </div>

            <!-- Timeline -->
            <div class="relative pl-8 space-y-6">
                <div class="absolute left-3.5 top-2 bottom-2 w-0.5 bg-gray-100"></div>
                <div class="relative">
                    <div class="absolute -left-5 w-3.5 h-3.5 rounded-full border-2 border-white bg-blue-500 shadow-sm"></div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Starts On</p>
                    <p class="text-sm font-bold text-gray-900" id="d-start">01 March 2024</p>
                </div>
                <div class="relative">
                    <div class="absolute -left-5 w-3.5 h-3.5 rounded-full border-2 border-white bg-red-500 shadow-sm"></div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Ends After</p>
                    <p class="text-sm font-bold text-gray-900" id="d-end">03 March 2024</p>
                </div>
            </div>

            <!-- Reason Section -->
            <div class="space-y-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Reason for Leave</p>
                <div class="bg-gray-50 rounded-3xl p-6 relative overflow-hidden">
                     <i data-lucide="quote" class="absolute right-4 bottom-4 w-12 h-12 text-gray-200 pointer-events-none"></i>
                     <p class="text-sm leading-relaxed text-gray-700 relative z-10" id="d-reason">
                         Detailed information about the leave request goes here...
                     </p>
                </div>
            </div>

            <!-- Submission Meta -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                <div>
                     <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Submitted At</p>
                     <p class="text-xs font-semibold text-gray-600" id="d-submitted">24 Feb 2024, 10:30 AM</p>
                </div>
                <div id="d-status-container">
                    <span class="badge" id="d-status">Pending</span>
                </div>
            </div>
        </div>

        <!-- Drawer Footer (Actions if Pending) -->
        <div class="p-8 border-t border-gray-100 sticky bottom-0 bg-white z-10 hidden" id="d-footer">
            <div class="grid grid-cols-2 gap-4">
                <button onclick="approveCurrentLeave()" class="flex items-center justify-center gap-2 bg-emerald-600 text-white py-3.5 rounded-2xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                    <i data-lucide="check" class="w-4 h-4"></i> Approve Leave
                </button>
                <button onclick="rejectCurrentLeave()" class="flex items-center justify-center gap-2 bg-red-50 text-red-600 py-3.5 rounded-2xl font-bold text-sm border border-red-100 hover:bg-red-100 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i> Reject Leave
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .drawer-overlay { background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); transition: all 0.3s ease; }
    .drawer-content { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .badge { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 9999px; }
    .status-pending { background: #fffbeb; color: #b45309; }
    .status-approved { background: #ecfdf5; color: #065f46; }
    .status-rejected { background: #fef2f2; color: #991b1b; }
    .q-filter.active { background: #000; color: #fff; border-color: #000; }
    .leave-row.table-row-hidden { display: none; }
</style>
@endpush

@push('scripts')
<script>
    function setQuickFilter(val) {
        document.getElementById('filter-status').value = val;
        syncPills();
        applyFilters();
    }

    function syncPills() {
        const val = document.getElementById('filter-status').value;
        document.querySelectorAll('.q-filter').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.val === val);
        });
    }

    function applyFilters() {
        const search = document.getElementById('search-input').value.toLowerCase().trim();
        const status = document.getElementById('filter-status').value;
        const rows = document.querySelectorAll('.leave-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.dataset.name;
            const rStatus = row.dataset.status;
            const isOnLeave = row.dataset.onleave === 'true';

            const searchMatch = !search || name.includes(search);
            let statusMatch = !status || rStatus === status;
            
            if (status === 'On Leave') {
                statusMatch = isOnLeave;
            }

            if (searchMatch && statusMatch) {
                row.classList.remove('table-row-hidden');
                visibleCount++;
            } else {
                row.classList.add('table-row-hidden');
            }
        });

        document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
    }

    function viewDetails(leaf) {
        currentLeaf = leaf;
        document.getElementById('d-id').textContent = 'LR-' + (1000 + leaf.id);
        document.getElementById('d-name').textContent = leaf.pro_name;
        document.getElementById('d-empid').textContent = 'ID: ' + leaf.empid;
        document.getElementById('d-avatar').textContent = leaf.pro_name.charAt(0);
        document.getElementById('d-category').textContent = leaf.category;
        document.getElementById('d-type').textContent = leaf.type;
        document.getElementById('d-days').textContent = leaf.days + (leaf.days === 1 ? ' Day' : ' Days');
        document.getElementById('d-start').textContent = leaf.start_date;
        document.getElementById('d-end').textContent = leaf.end_date;
        document.getElementById('d-reason').textContent = leaf.reason;
        document.getElementById('d-submitted').textContent = leaf.submitted_at;
        
        const statusBadge = document.getElementById('d-status');
        statusBadge.className = 'badge status-' + leaf.status.toLowerCase();
        statusBadge.textContent = leaf.status;
        
        const footer = document.getElementById('d-footer');
        if (footer) {
            footer.classList.toggle('hidden', leaf.status !== 'Pending');
        }

        const overlay = document.getElementById('drawer-overlay');
        const content = document.getElementById('drawer-content');
        overlay.classList.remove('hidden');
        setTimeout(() => content.classList.remove('translate-x-full'), 10);
        lucide.createIcons();
    }

    function closeDrawer(e) {
        if (e && e.target !== document.getElementById('drawer-overlay')) return;
        const overlay = document.getElementById('drawer-overlay');
        const content = document.getElementById('drawer-content');
        content.classList.add('translate-x-full');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    function approveCurrentLeave() {
        if (!currentLeaf) return;
        confirmApprove(currentLeaf.id, currentLeaf.pro_name);
    }

    function rejectCurrentLeave() {
        if (!currentLeaf) return;
        confirmReject(currentLeaf.id, currentLeaf.pro_name);
    }

    function confirmApprove(id, name) {
        Swal.fire({
            title: 'Approve Leave?',
            html: `Are you sure you want to approve the leave request for <b>${name}</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Yes, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`approve-form-${id}`).submit();
            }
        });
    }

    function confirmReject(id, name) {
        Swal.fire({
            title: 'Reject Leave Request?',
            text: `Are you sure you want to reject the leave request for ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Reject'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`reject-form-${id}`).submit();
            }
        });
    }
</script>
@endpush
@endsection
