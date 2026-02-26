@extends('layouts.app')

@section('title', 'Kit Products · Bellavella Admin')

@section('content')
<div class="flex flex-col gap-6">

    <!-- ── Page Header ──────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Kit Products</h2>
            <p class="text-sm text-gray-400 mt-0.5">Manage salon kit inventory across all service types</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="exportProducts()" class="flex items-center gap-2 border border-gray-200 bg-white text-gray-700 px-5 py-2.5 rounded-full hover:bg-gray-50 transition-all font-medium text-sm">
                <i data-lucide="download" class="w-4 h-4"></i> Export
            </button>
            <a href="{{ route('kit-products.create') }}" class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Product
            </a>
        </div>
    </div>

    @if($lowStockCount > 0 || $outOfStockCount > 0)
    <!-- ── Alert Banner ──────────────────────────────────────── -->
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4">
        <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-700">Stock Alert</p>
            <p class="text-xs text-amber-500">
                {{ $lowStockCount }} product{{ $lowStockCount != 1 ? 's' : '' }} running low
                @if($outOfStockCount > 0) · {{ $outOfStockCount }} out of stock @endif.
                Consider restocking soon.
            </p>
        </div>
    </div>
    @endif

    <!-- ── Stat Cards ────────────────────────────────────────── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total SKUs</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalItems }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="package" class="w-6 h-6 text-gray-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Low Stock</p>
                <p class="text-3xl font-bold text-gray-900">{{ $lowStockCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Out of Stock</p>
                <p class="text-3xl font-bold text-gray-900">{{ $outOfStockCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Inventory Value</p>
                <p class="text-3xl font-bold text-gray-900">&#x20B9;{{ number_format($totalValue) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="indian-rupee" class="w-6 h-6 text-emerald-500"></i>
            </div>
        </div>
    </div>

    <!-- ── Products Table ────────────────────────────────────── -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Toolbar -->
        <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input id="search-input" type="text" placeholder="Search by name, SKU, brand…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
            </div>
            <select id="filter-stock" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                <option value="">All Stock Levels</option>
                <option value="in">In Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
            <select id="filter-status" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <span id="filter-count" class="text-xs text-gray-400 ml-auto whitespace-nowrap"></span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKU</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Price</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="products-tbody">
                    @foreach($products as $p)
                        @php
                            $avail = $p->available_stock;
                            $min = $p->min_stock;
                            $total = $p->total_stock;

                            if ($avail == 0) {
                                $stockClass = 'bg-red-50 text-red-600';
                                $stockLabel = 'Out of Stock';
                                $stockAttr = 'out';
                            } elseif ($avail <= $min) {
                                $stockClass = 'bg-amber-50 text-amber-600';
                                $stockLabel = 'Low Stock';
                                $stockAttr = 'low';
                            } else {
                                $stockClass = 'bg-emerald-50 text-emerald-700';
                                $stockLabel = 'In Stock';
                                $stockAttr = 'in';
                            }

                            $barPct = $total > 0 ? min(100, round($avail / $total * 100)) : 0;
                            $barColor = $avail == 0 ? 'bg-red-400' : ($avail <= $min ? 'bg-amber-400' : 'bg-emerald-400');
                            $statusClass = $p->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500';
                        @endphp
                        <tr class="product-row table-row transition-all"
                            data-name="{{ strtolower($p->name) }}"
                            data-sku="{{ strtolower($p->sku) }}"
                            data-brand="{{ strtolower($p->brand) }}"
                            data-category="{{ $p->category }}"
                            data-stock="{{ $stockAttr }}"
                            data-status="{{ $p->status }}">

                            <td class="px-6 py-4">
                                <span class="text-xs font-mono font-bold text-gray-500">{{ $p->sku }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $p->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $p->brand }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 min-w-[120px]">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold text-gray-800">{{ number_format($avail) }} <span class="font-normal text-gray-400">/ {{ number_format($total) }}</span></span>
                                    </div>
                                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full" style="width:{{ $barPct }}%"></div>
                                    </div>
                                    <span class="badge {{ $stockClass }} mt-0.5" style="font-size:9px">{{ $stockLabel }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">&#x20B9;{{ number_format($p->price) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $statusClass }}">{{ $p->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('kit-products.edit', $p->id) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-black" title="Edit Product">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('kit-products.destroy', $p->id) }}" method="POST" class="inline" id="delete-form-{{ $p->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->kit_orders_count ?? 0 }})" class="p-2 hover:bg-red-50 rounded-lg transition-colors text-gray-400 hover:text-red-500" title="Delete Product">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="no-results" class="hidden py-16 text-center">
                <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                <p class="text-sm text-gray-400">No products match your filters.</p>
            </div>
        </div>

        <!-- Table Footer -->
        <div class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-400">Showing <span id="visible-count">{{ $totalItems }}</span> of {{ $totalItems }} products</p>
            <p class="text-xs text-gray-400">Total available value: <span class="font-semibold text-gray-700">&#x20B9;{{ number_format($totalValue) }}</span></p>
        </div>
    </div>

</div>

@push('styles')
<style>
    .product-row.hidden-row { display: none; }
    .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        const q = document.getElementById('search-input').value.toLowerCase().trim();
        const stock = document.getElementById('filter-stock').value;
        const status = document.getElementById('filter-status').value;
        const rows = document.querySelectorAll('.product-row');
        let visible = 0;

        rows.forEach(row => {
            const nameMatch = !q || row.dataset.name.includes(q) || row.dataset.sku.includes(q) || row.dataset.brand.includes(q);
            const stockMatch = !stock || row.dataset.stock === stock;
            const statMatch = !status || row.dataset.status === status;

            if (nameMatch && stockMatch && statMatch) {
                row.classList.remove('hidden-row');
                visible++;
            } else {
                row.classList.add('hidden-row');
            }
        });

        document.getElementById('visible-count').textContent = visible;
        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
    }

    function confirmDelete(id, name, assignedStock) {
        if (assignedStock > 0) {
            Swal.fire({
                title: 'Cannot Delete',
                html: `<b>${name}</b> is currently assigned to <b>${assignedStock}</b> professional${assignedStock>1?'s':''}.<br>Return all kits before deleting.`,
                icon: 'warning',
                confirmButtonColor: '#111'
            });
            return;
        }

        Swal.fire({
            title: `Delete "${name}"?`,
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function exportProducts() {
        Swal.fire({ title: 'Exporting…', text: 'Generating CSV export.', icon: 'info', timer: 1500, showConfirmButton: false });
    }
</script>
@endpush
@endsection
