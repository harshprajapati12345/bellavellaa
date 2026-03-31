@extends('layouts.app')

@section('title', 'Kit Order History · Bellavella Admin')

@section('content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Kit Order History</h2>
            <p class="text-sm text-gray-400 mt-0.5">Track and manage delivery status for all kit assignments</p>
        </div>
        <a href="{{ route('kit-orders.index') }}"
           class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-5 py-2.5 rounded-full hover:bg-gray-50 transition-all font-medium text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Kit Orders
        </a>
    </div>

    {{-- Status Summary Tabs --}}
    <div class="flex flex-wrap gap-3">
        @php
            $tabColors = [
                'Pending'    => 'bg-amber-50 text-amber-600 border-amber-200',
                'Dispatched' => 'bg-blue-50 text-blue-600 border-blue-200',
                'Delivered'  => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                'Returned'   => 'bg-purple-50 text-purple-600 border-purple-200',
                'Lost'       => 'bg-red-50 text-red-500 border-red-200',
            ];
            $activeStatus = request('status');
        @endphp

        <a href="{{ route('kit-orders.history', request()->except('status', 'page')) }}"
           class="px-4 py-2 rounded-full text-sm font-semibold border transition-all
                  {{ !$activeStatus ? 'bg-black text-white border-black' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
            All <span class="ml-1 font-normal opacity-60">{{ $statusCounts->sum() }}</span>
        </a>

        @foreach($statuses as $status)
            <a href="{{ route('kit-orders.history', array_merge(request()->except('page'), ['status' => $status])) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold border transition-all
                      {{ $activeStatus === $status
                           ? $tabColors[$status] . ' ring-1'
                           : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300' }}">
                {{ $status }}
                <span class="ml-1 font-normal opacity-60">{{ $statusCounts[$status] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('kit-orders.history') }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row gap-3">
        <input type="hidden" name="status" value="{{ request('status') }}">

        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by professional, kit or order ID…"
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
        </div>

        <select name="professional_id"
                class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2.5 focus:ring-2 focus:ring-black/5 outline-none min-w-[200px]">
            <option value="">All Professionals</option>
            @foreach($professionals as $id => $name)
                <option value="{{ $id }}" {{ request('professional_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-black text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-800 transition-all">
            Filter
        </button>
        <a href="{{ route('kit-orders.history') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all">
            Reset
        </a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/60">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Order</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kit Product</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Qty</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Assigned</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        @php
                            $statusStyles = [
                                'Pending'    => 'bg-amber-50 text-amber-600',
                                'Dispatched' => 'bg-blue-50 text-blue-600',
                                'Delivered'  => 'bg-emerald-50 text-emerald-700',
                                'Returned'   => 'bg-purple-50 text-purple-600',
                                'Lost'       => 'bg-red-50 text-red-500',
                                'Assigned'   => 'bg-gray-100 text-gray-600',
                            ];
                            $style = $statusStyles[$order->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors group" id="row-{{ $order->id }}">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-gray-400">#K-{{ $order->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->kitProduct->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $order->kitProduct->category ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-700">{{ $order->professional->name }}</span>
                                <p class="text-xs text-gray-400">{{ $order->professional->phone ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-gray-800">{{ $order->quantity }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-500">
                                    {{ $order->assigned_at ? \Carbon\Carbon::parse($order->assigned_at)->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span id="status-badge-{{ $order->id }}"
                                      class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $style }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}', '{{ addslashes($order->notes ?? '') }}')"
                                        class="text-xs font-semibold text-gray-500 hover:text-black border border-gray-200 hover:border-gray-400 px-3 py-1.5 rounded-lg transition-all">
                                    Update Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <i data-lucide="package-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                                <p class="text-sm text-gray-400">No kit orders found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="p-5 border-t border-gray-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Update Status Modal --}}
<div id="status-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Update Delivery Status</h3>
            <p class="text-sm text-gray-400 mb-6">Order <span id="modal-order-id" class="font-semibold text-gray-700"></span></p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">New Status</label>
                    <div class="grid grid-cols-2 gap-2" id="status-options">
                        @foreach(['Pending', 'Dispatched', 'Delivered', 'Returned', 'Lost'] as $s)
                            @php
                                $colors = [
                                    'Pending'    => 'hover:bg-amber-50 hover:border-amber-300 hover:text-amber-700',
                                    'Dispatched' => 'hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700',
                                    'Delivered'  => 'hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700',
                                    'Returned'   => 'hover:bg-purple-50 hover:border-purple-300 hover:text-purple-700',
                                    'Lost'       => 'hover:bg-red-50 hover:border-red-300 hover:text-red-600',
                                ];
                            @endphp
                            <button type="button" data-status="{{ $s }}"
                                    onclick="selectStatus('{{ $s }}')"
                                    class="status-option px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 transition-all {{ $colors[$s] }}">
                                {{ $s }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" id="selected-status" value="">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Notes (optional)</label>
                    <textarea id="modal-notes" rows="2" placeholder="Tracking number, remarks…"
                              class="w-full bg-gray-50 border-none rounded-xl text-sm px-4 py-3 focus:ring-2 focus:ring-black/5 outline-none resize-none"></textarea>
                </div>
            </div>

            <div id="modal-error" class="hidden mt-3 text-xs text-red-500 font-medium"></div>

            <div class="flex gap-3 mt-6">
                <button onclick="closeStatusModal()"
                        class="flex-1 px-6 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="submitStatusUpdate()"
                        id="submit-btn"
                        class="flex-1 px-6 py-3 bg-black text-white rounded-xl text-sm font-semibold hover:bg-gray-900 transition-colors shadow-lg shadow-black/10 disabled:opacity-50">
                    Save Status
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentOrderId = null;

    const statusColors = {
        Pending:    'bg-amber-50 text-amber-600',
        Dispatched: 'bg-blue-50 text-blue-600',
        Delivered:  'bg-emerald-50 text-emerald-700',
        Returned:   'bg-purple-50 text-purple-600',
        Lost:       'bg-red-50 text-red-500',
        Assigned:   'bg-gray-100 text-gray-600',
    };

    const selectedColors = {
        Pending:    'bg-amber-50 border-amber-300 text-amber-700',
        Dispatched: 'bg-blue-50 border-blue-300 text-blue-700',
        Delivered:  'bg-emerald-50 border-emerald-300 text-emerald-700',
        Returned:   'bg-purple-50 border-purple-300 text-purple-700',
        Lost:       'bg-red-50 border-red-300 text-red-600',
    };

    function openStatusModal(orderId, currentStatus, currentNotes) {
        currentOrderId = orderId;
        document.getElementById('modal-order-id').textContent = '#K-' + orderId;
        document.getElementById('modal-notes').value = currentNotes || '';
        document.getElementById('modal-error').classList.add('hidden');
        document.getElementById('selected-status').value = '';

        // Reset all buttons
        document.querySelectorAll('.status-option').forEach(btn => {
            const s = btn.dataset.status;
            btn.className = btn.className.replace(/bg-\S+ border-\S+ text-\S+/g, '');
            if (btn.dataset.status === currentStatus) {
                btn.classList.add(...selectedColors[s].split(' '));
            }
        });

        document.getElementById('status-modal').classList.remove('hidden');
        document.getElementById('status-modal').classList.add('flex');
    }

    function closeStatusModal() {
        document.getElementById('status-modal').classList.remove('flex');
        document.getElementById('status-modal').classList.add('hidden');
        currentOrderId = null;
    }

    function selectStatus(status) {
        document.getElementById('selected-status').value = status;
        document.querySelectorAll('.status-option').forEach(btn => {
            const s = btn.dataset.status;
            btn.classList.remove('bg-amber-50','border-amber-300','text-amber-700',
                                 'bg-blue-50','border-blue-300','text-blue-700',
                                 'bg-emerald-50','border-emerald-300','text-emerald-700',
                                 'bg-purple-50','border-purple-300','text-purple-700',
                                 'bg-red-50','border-red-300','text-red-600');
            if (s === status) {
                btn.classList.add(...selectedColors[s].split(' '));
            }
        });
    }

    async function submitStatusUpdate() {
        const status = document.getElementById('selected-status').value;
        if (!status) {
            const err = document.getElementById('modal-error');
            err.textContent = 'Please select a status.';
            err.classList.remove('hidden');
            return;
        }

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        const baseUrl = window.Laravel ? window.Laravel.baseUrl : window.location.origin;
        const url = `${baseUrl}/professionals/kit-orders/${currentOrderId}/status`;

        try {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status: status,
                    notes:  document.getElementById('modal-notes').value,
                }),
            });

            if (!res.ok) {
                const text = await res.text();
                console.error('Server error:', res.status, text);
                document.getElementById('modal-error').textContent = `Server error (${res.status}). Please try again.`;
                document.getElementById('modal-error').classList.remove('hidden');
                return;
            }

            const data = await res.json();
            if (data.success) {
                // Update badge in table without page reload
                const badge = document.getElementById('status-badge-' + currentOrderId);
                if (badge) {
                    badge.textContent = data.status;
                    badge.className = 'px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ' + (statusColors[data.status] ?? 'bg-gray-100 text-gray-600');
                }
                closeStatusModal();
                showToast(data.message, true);
            } else {
                document.getElementById('modal-error').textContent = data.message || 'Update failed.';
                document.getElementById('modal-error').classList.remove('hidden');
            }
        } catch (e) {
            console.error('Fetch exception:', e);
            document.getElementById('modal-error').textContent = 'Network error: ' + e.message;
            document.getElementById('modal-error').classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Status';
        }
    }

    function showToast(msg, success = true) {
        const t = document.createElement('div');
        t.className = `fixed bottom-6 right-6 z-[9999] px-5 py-3 rounded-2xl shadow-lg text-sm font-semibold flex items-center gap-2 transition-all
                       ${success ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'}`;
        t.innerHTML = `<span>${success ? '✅' : '❌'}</span> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3500);
    }
</script>
@endpush
@endsection
