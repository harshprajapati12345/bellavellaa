@extends('layouts.app')
@php $pageTitle = 'Withdrawal History'; @endphp

@section('content')
<div class="flex flex-col gap-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Withdrawal History</h2>
            <p class="text-sm text-gray-400 mt-0.5">Track all processed payout transactions</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('professionals.withdraw-history') }}" method="GET" class="flex items-center gap-3">
                <select name="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black/5 decoration-none transition-all">
                    <option value="">All Status</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name / phone..."
                        class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-64 transition-all">
                </div>
                <button type="submit" class="bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Amount</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Method</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Details / Reference</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Processed On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->professional->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($item->professional->name) }}" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->professional->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->professional->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-lg font-bold text-gray-900">₹{{ number_format($item->amount / 100, 2) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $item->method }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($item->status === 'paid' || $item->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>{{ $item->status === 'completed' ? 'Completed' : 'Paid' }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-500">
                                <i data-lucide="x-circle" class="w-3 h-3"></i>Rejected
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($item->status === 'paid' || $item->status === 'completed')
                                <div class="flex flex-col gap-1.5">
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-none">Paid via {{ $item->method }}</p>
                                    <div class="flex flex-col gap-0.5">
                                        @if($item->method === 'upi')
                                            <span class="text-xs font-semibold text-gray-900 leading-tight">VPA: {{ $item->upi_id }}</span>
                                        @elseif($item->method === 'bank')
                                            <span class="text-xs font-semibold text-gray-900 leading-tight">A/C: {{ $item->account_number }}</span>
                                            <span class="text-[10px] text-gray-400 leading-tight">{{ $item->account_holder }} • {{ $item->bank_name }}</span>
                                        @else
                                            <span class="text-xs font-semibold text-gray-900 leading-tight">Direct Wallet payout</span>
                                        @endif
                                        @if($item->transaction_reference)
                                        <p class="text-xs font-mono text-emerald-600 mt-1">Ref: {{ $item->transaction_reference }}</p>
                                        @endif
                                    </div>
                                    @if($item->admin_note)
                                        <p class="text-[10px] text-gray-400 italic">Note: {{ $item->admin_note }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm text-red-500 font-medium">Rejected: {{ $item->rejection_reason ?? $item->admin_note ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400">Amount refunded to wallet</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $item->processed_at ? $item->processed_at->format('d M Y, h:i A') : $item->updated_at->format('d M Y, h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                <i data-lucide="history" class="w-8 h-8 text-gray-200"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No history found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $history->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
