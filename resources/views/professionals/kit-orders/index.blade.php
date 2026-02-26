@extends('layouts.app')

@section('title', 'Kit Orders · Bellavella Admin')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Kit Orders</h2>
            <p class="text-sm text-gray-400 mt-0.5">Manage professional kit purchases and stock tracking</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="showAssignModal()" class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10">
                <i data-lucide="plus" class="w-4 h-4"></i> Assign Kit
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Assigned</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalKits }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="package" class="w-6 h-6 text-gray-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Low Stock</p>
                <p class="text-3xl font-bold text-gray-900">{{ $lowStock }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Out of Stock</p>
                <p class="text-3xl font-bold text-gray-900">{{ $outStock }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Allocation Value</p>
                <p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalValue) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Toolbar -->
        <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input id="search-input" type="text" placeholder="Search by name or professional…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
            </div>
            <span id="filter-count" class="text-xs text-gray-400 ml-auto whitespace-nowrap"></span>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kit Details</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Purchased</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Used</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Remaining</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="orders-tbody">
                    @forelse($orders as $order)
                        @php
                            $remaining = $order->quantity - $order->used_quantity;
                            $isOut = $remaining === 0;
                            $isLow = $remaining > 0 && $remaining <= 5;

                            if($isOut) {
                                $statusLabel = "Out of Stock";
                                $statusClass = "bg-red-50 text-red-600";
                                $rowClass    = "out-of-stock";
                            } elseif($isLow) {
                                $statusLabel = "Low Stock";
                                $statusClass = "bg-amber-50 text-amber-600";
                                $rowClass    = "";
                            } else {
                                $statusLabel = "Available";
                                $statusClass = "bg-emerald-50 text-emerald-600";
                                $rowClass    = "";
                            }
                        @endphp
                        <tr class="kit-row table-row transition-all {{ $rowClass }}"
                            data-id="K-{{ 100 + $order->id }}"
                            data-name="{{ strtolower($order->kitProduct->name) }}"
                            data-professional="{{ strtolower($order->professional->name) }}">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-gray-400 tracking-tighter">K-{{ 100 + $order->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->kitProduct->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $order->kitProduct->category }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-700">{{ $order->professional->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $order->quantity }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-800">
                                {{ $order->used_quantity }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold {{ $isLow ? 'text-amber-600' : 'text-gray-900' }}">
                                    {{ $remaining }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No assignments found</td>
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

<!-- Modal for assignment -->
<div id="assign-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
        <form action="{{ route('kit-orders.store') }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-xl font-bold text-gray-900 mb-6">Assign Kit Product</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Professional</label>
                    <select name="professional_id" required class="w-full bg-gray-50 border-none rounded-xl text-sm px-4 py-3 focus:ring-2 focus:ring-black/5 outline-none">
                        <option value="">Select Professional</option>
                        @foreach($professionals as $pro)
                            <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Kit Product</label>
                    <select name="kit_product_id" required class="w-full bg-gray-50 border-none rounded-xl text-sm px-4 py-3 focus:ring-2 focus:ring-black/5 outline-none">
                        <option value="">Select Product</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->available_stock }} available)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" required class="w-full bg-gray-50 border-none rounded-xl text-sm px-4 py-3 focus:ring-2 focus:ring-black/5 outline-none">
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="button" onclick="hideAssignModal()" class="flex-1 px-6 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-black text-white rounded-xl text-sm font-semibold hover:bg-gray-900 transition-colors shadow-lg shadow-black/10">Assign Now</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .kit-row.hidden-row { display: none; }
    .kit-row.out-of-stock { background: #fff5f5; }
    .kit-row.out-of-stock:hover { background: #fee2e2; }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        const search = document.getElementById('search-input').value.toLowerCase();
        const rows = document.querySelectorAll('.kit-row');
        let visible = 0;

        rows.forEach(row => {
            const show = !search || 
                         row.dataset.name.includes(search) || 
                         row.dataset.professional.includes(search) || 
                         row.dataset.id.includes(search);
            row.classList.toggle('hidden-row', !show);
            if (show) visible++;
        });

        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        document.getElementById('filter-count').textContent = visible === rows.length ? '' : `${visible} of ${rows.length} shown`;
    }

    function showAssignModal() {
        document.getElementById('assign-modal').classList.remove('hidden');
        document.getElementById('assign-modal').classList.add('flex');
    }

    function hideAssignModal() {
        document.getElementById('assign-modal').classList.remove('flex');
        document.getElementById('assign-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
