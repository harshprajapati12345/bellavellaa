@extends('layouts.app')

@section('title', 'Deposit History · Bellavella Admin')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Deposit & Withdrawal History</h2>
            <p class="text-sm text-gray-400 mt-0.5">Track all wallet transactions, earnings, and payouts for professionals</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Transactions</p>
                <p class="text-3xl font-bold text-gray-900">{{ $transactions->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="activity" class="w-6 h-6 text-gray-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Total Credited</p>
                <p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalDeposits, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="arrow-down-left" class="w-6 h-6 text-emerald-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Total Debited</p>
                <p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalWithdrawals, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="arrow-up-right" class="w-6 h-6 text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Toolbar -->
        <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input id="search-input" type="text" placeholder="Search by TR ID or Professional…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <select id="type-filter" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 pr-8 focus:ring-2 focus:ring-black/5 outline-none appearance-none cursor-pointer">
                    <option value="">All Types</option>
                    <option value="credit">Credit</option>
                    <option value="debit">Debit</option>
                </select>
            </div>
            <span id="filter-count" class="text-xs text-gray-400 ml-auto whitespace-nowrap"></span>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaction ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date & Time</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Type</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Amount</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Bal. After</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="transactions-tbody">
                    @forelse($transactions as $trx)
                        <tr class="trx-row table-row transition-all hover:bg-gray-50 cursor-pointer"
                            data-id="{{ strtolower($trx['id']) }}"
                            data-type="{{ strtolower($trx['type']) }}"
                            data-professional="{{ strtolower($trx['professional']) }}">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-gray-400 tracking-tighter">{{ $trx['id'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($trx['date'])->format('d M, Y h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $trx['professional'] }}</span>
                                    <span class="text-xs text-gray-400">ID: {{ $trx['pro_id'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(strtolower($trx['type']) === 'credit')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                        Credit
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600">
                                        Debit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ strtolower($trx['type']) === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ strtolower($trx['type']) === 'credit' ? '+' : '-' }}₹{{ number_format($trx['amount'], 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">
                                    ₹{{ number_format($trx['balance_after'], 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col max-w-[200px]">
                                    <span class="text-sm font-medium text-gray-700 truncate" title="{{ $trx['description'] }}">{{ $trx['description'] ?? 'No description' }}</span>
                                    <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $trx['source']) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No transactions found</td>
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

@push('styles')
<style>
    .trx-row.hidden-row { display: none; }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        const search = document.getElementById('search-input').value.toLowerCase();
        const typeFilt = document.getElementById('type-filter').value.toLowerCase();
        const rows = document.querySelectorAll('.trx-row');
        let visible = 0;

        rows.forEach(row => {
            const matchesSearch = !search || 
                                  row.dataset.id.includes(search) || 
                                  row.dataset.professional.includes(search);
            const matchesType = !typeFilt || row.dataset.type === typeFilt;
            
            const show = matchesSearch && matchesType;
            row.classList.toggle('hidden-row', !show);
            if (show) visible++;
        });

        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        document.getElementById('filter-count').textContent = visible === rows.length ? '' : `${visible} of ${rows.length} shown`;
    }
</script>
@endpush
@endsection
