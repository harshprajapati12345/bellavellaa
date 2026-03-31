@extends('layouts.app')
@php $pageTitle = 'Scratch Cards'; @endphp

@section('content')
<div class="flex flex-col gap-6">

    <!-- ── Page Header ──────────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Scratch Card Management</h2>
            <p class="text-sm text-gray-400 mt-0.5">Control rewards, distribution and manual triggers</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openCreateModal()"
                class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
                <i data-lucide="ticket-plus" class="w-4 h-4"></i> Create Scratch Card
            </button>
        </div>
    </div>

    <!-- ── Stats Overview ─────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 ring-1 ring-black/5 hover:shadow-md transition-all">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Cards</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 ring-1 ring-black/5 hover:shadow-md transition-all">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pending</p>
            <h3 class="text-2xl font-bold text-orange-500">{{ number_format($stats['pending']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 ring-1 ring-black/5 hover:shadow-md transition-all">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Scratched</p>
            <h3 class="text-2xl font-bold text-green-500">{{ number_format($stats['scratched']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 ring-1 ring-black/5 hover:shadow-md transition-all">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Rewarded</p>
            <h3 class="text-2xl font-bold text-black">₹{{ number_format($stats['total_rewarded']) }}</h3>
        </div>
    </div>

    <!-- ── Filter Bar ─────────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('scratch-cards.index') }}" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="relative min-w-[200px] flex-1">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-black/5 focus:border-black/40 outline-none transition-all">
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()"
                class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none cursor-pointer">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="scratched" {{ request('status') === 'scratched' ? 'selected' : '' }}>Scratched</option>
            </select>

            <!-- Source Filter -->
            <select name="source" onchange="this.form.submit()"
                class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none cursor-pointer">
                <option value="">All Sources</option>
                <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="payment" {{ request('source') === 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="referral" {{ request('source') === 'referral' ? 'selected' : '' }}>Referral</option>
            </select>

            <!-- Clear -->
            @if(request()->anyFilled(['search', 'status', 'source']))
                <a href="{{ route('scratch-cards.index') }}" class="text-sm text-red-500 font-medium hover:underline">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- ── Table Layout ─────────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]" id="cards-table">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Customer</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Reward</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Source</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Timing</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                    <tr class="table-row border-b border-gray-50 hover:bg-gray-50/50 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-gray-700 font-bold ring-2 ring-gray-50 text-sm">
                                    {{ substr($card->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $card->customer->name ?? 'Deleted User' }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $card->customer->mobile ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">₹{{ number_format($card->amount) }}</span>
                                <span class="text-[10px] text-gray-400">{{ $card->title ?? 'Scratch Card' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ 
                                $card->source === 'payment' ? 'bg-blue-50 text-blue-600' : 
                                ($card->source === 'referral' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600') 
                            }}">
                                {{ $card->source }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($card->is_scratched)
                                <div class="flex items-center gap-1.5 text-green-600">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                    <span class="text-xs font-semibold">Scratched</span>
                                </div>
                            @else
                                @if($card->expires_at && now()->gt($card->expires_at))
                                    <div class="flex items-center gap-1.5 text-red-500">
                                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                        <span class="text-xs font-semibold">Expired</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5 text-orange-500">
                                        <i data-lucide="timer" class="w-4 h-4"></i>
                                        <span class="text-xs font-semibold">Pending</span>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <p class="text-xs text-gray-500"><span class="text-[10px] uppercase font-bold text-gray-300">Created:</span> {{ $card->created_at->format('d M, Y') }}</p>
                                @if($card->is_scratched)
                                    <p class="text-[11px] text-green-600 font-medium"><span class="text-[10px] uppercase font-bold text-gray-300">Revealed:</span> {{ $card->scratched_at->format('d M, H:i') }}</p>
                                @elseif($card->expires_at)
                                    <p class="text-[11px] {{ now()->gt($card->expires_at) ? 'text-red-400' : 'text-gray-400' }}"><span class="text-[10px] uppercase font-bold text-gray-300">Expires:</span> {{ $card->expires_at->format('d M, Y') }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if(!$card->is_scratched && (!$card->expires_at || now()->lt($card->expires_at)))
                            <button onclick="forceScratch(this, {{ $card->id }}, '{{ addslashes($card->customer->name ?? 'this user') }}', {{ $card->amount }})"

                                class="btn-force-scratch inline-flex items-center gap-1.5 px-3 py-1.5 bg-black text-white rounded-xl hover:bg-gray-800 transition-all text-[11px] font-bold shadow-md shadow-black/5">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                Force Scratch
                            </button>
                            @else
                            <i data-lucide="minus" class="w-4 h-4 text-gray-200 inline-block"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-2">
                                    <i data-lucide="ticket-slash" class="w-6 h-6 text-gray-300"></i>
                                </div>
                                <p class="text-gray-400 text-sm font-medium italic">No scratch cards found with these filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cards->hasPages())
        <div class="px-5 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $cards->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ── Create Modal (AJAX SEARCH) ─────────────────────────────────────────────────── -->
<div id="create-modal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 border border-white">
        <div class="px-8 pt-8 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Create Scratch Reward</h3>
                <p class="text-xs text-gray-400 font-medium">Issue a manual scratch card to a customer</p>
            </div>
            <button onclick="closeCreateModal()" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-black transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="{{ route('scratch-cards.store') }}" method="POST" class="p-8 space-y-5" onsubmit="disableSubmitButton(this)">
            @csrf
            <!-- Customer AJAX Search -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Target Customer</label>
                <div class="relative">
                    <input type="text" id="customer-ajax-search" placeholder="Search by name or mobile..."
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-4 focus:ring-black/5 outline-none transition-all placeholder:text-gray-300"
                        onkeyup="searchCustomers(this.value)">
                    <input type="hidden" name="customer_id" id="selected-customer-id" required>
                    
                    <!-- Search Results Overlay -->
                    <div id="search-results" class="absolute z-10 left-0 right-0 top-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl hidden max-h-48 overflow-y-auto ring-1 ring-black/5">
                        <!-- Ajax items here -->
                    </div>
                </div>
                <p id="selected-customer-display" class="text-[11px] font-bold text-green-600 pl-1 hidden"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Reward Value (₹)</label>
                    <input type="number" name="amount" required min="1" placeholder="e.g. 50" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Expiry (Optional)</label>
                    <input type="date" name="expires_at" min="{{ date('Y-m-d') }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Title & Description</label>
                <input type="text" name="title" placeholder="Reward Title (e.g. Bonus Surprise)" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm mb-3">
                <textarea name="description" rows="2" placeholder="Message for the user..." class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" id="submit-button" class="w-full py-4.5 bg-black text-white rounded-2xl font-bold shadow-xl shadow-black/10 hover:bg-gray-900 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-5 h-5"></i>
                    Issue Scratch Card
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // AJAX Customer Search
    let searchTimeout;
    function searchCustomers(query) {
        if (query.length < 2) {
            document.getElementById('search-results').classList.add('hidden');
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('scratch-cards.search-customers') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    const results = document.getElementById('search-results');
                    results.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(customer => {
                            const div = document.createElement('div');
                            div.className = 'px-5 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-all';
                            div.innerHTML = `
                                <p class="text-sm font-bold text-gray-900">${customer.name}</p>
                                <p class="text-[10px] text-gray-400">${customer.mobile}</p>
                            `;
                            div.onclick = () => selectCustomer(customer);
                            results.appendChild(div);
                        });
                        results.classList.remove('hidden');
                    } else {
                        results.innerHTML = '<div class="px-5 py-4 text-xs text-center text-gray-400">No customers found</div>';
                        results.classList.remove('hidden');
                    }
                });
        }, 300);
    }

    function selectCustomer(customer) {
        document.getElementById('selected-customer-id').value = customer.id;
        document.getElementById('customer-ajax-search').value = '';
        document.getElementById('search-results').classList.add('hidden');
        
        const display = document.getElementById('selected-customer-display');
        display.innerHTML = `<i data-lucide="check" class="w-3 h-3 inline"></i> Selected: ${customer.name} (${customer.mobile})`;
        display.classList.remove('hidden');
        lucide.createIcons(); // Refresh icons
    }

    function disableSubmitButton(form) {
        const btn = form.querySelector('#submit-button');
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Issuing...';
        lucide.createIcons();
    }

    function forceScratch(btn, id, name, amount) {
        Swal.fire({
            title: `Force Reveal?`,
            text: `Confirm marking ₹${amount} card as scratched for ${name}. This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000000',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Yes, Force Scratch',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Disable existing button to prevent double submission
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>';
                    lucide.createIcons();
                }


                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/scratch-cards/${id}/force-scratch`;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<style>
    .py-4\.5 { padding-top: 1.125rem; padding-bottom: 1.125rem; }
</style>
@endpush
