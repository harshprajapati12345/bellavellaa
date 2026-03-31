@extends('layouts.app')
@php $pageTitle = 'Withdraw Requests'; @endphp

@section('content')
<div class="flex flex-col gap-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Withdraw Requests</h2>
            <p class="text-sm text-gray-400 mt-0.5">Review and process professional payout requests</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('professionals.withdraw-requests') }}" method="GET" class="flex items-center gap-3">
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
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Details</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $req->professional->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($req->professional->name) }}" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $req->professional->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $req->professional->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-lg font-bold text-gray-900">₹{{ number_format($req->amount / 100, 2) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($req->method === 'upi')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                                <i data-lucide="smartphone" class="w-3 h-3"></i>UPI
                            </span>
                            @elseif($req->method === 'bank')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600">
                                <i data-lucide="building-2" class="w-3 h-3"></i>Bank
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-50 text-gray-600">
                                <i data-lucide="zap" class="w-3 h-3"></i>Direct
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm text-gray-600">
                                @if($req->method === 'upi')
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900">{{ $req->upi_id }}</span>
                                        <span class="text-xs text-gray-400">UPI / VPA</span>
                                    </div>
                                @elseif($req->method === 'bank')
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-gray-900">{{ $req->account_number }}</span>
                                        </div>
                                        <div class="flex flex-col text-[11px] text-gray-400 leading-tight">
                                            <span>{{ $req->account_holder }}</span>
                                            <span>{{ $req->bank_name }} • {{ $req->ifsc_code }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900">N/A</span>
                                        <span class="text-xs text-gray-400">Direct Wallet payout</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $req->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($req->status === 'pending')
                                    <a href="{{ route('admin.professionals.withdraw.approve', $req->id) }}" 
                                            class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors"
                                            title="Approve"
                                            onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.professionals.withdraw.reject', $req->id) }}"
                                            class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors"
                                            title="Reject"
                                            onclick="return confirm('Are you sure you want to reject this withdrawal? The amount will be refunded.')">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </a>
                                @elseif($req->status === 'completed' || $req->status === 'paid')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg bg-red-50 text-red-600">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>Rejected
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                <i data-lucide="inbox" class="w-8 h-8 text-gray-200"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No withdrawal requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Simple confirmation logic for links
</script>
@endpush
