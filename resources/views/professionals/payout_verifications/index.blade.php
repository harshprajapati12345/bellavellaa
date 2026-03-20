@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payout Verifications</h1>
            <p class="text-gray-500 text-sm">Review and approve professional bank/UPI details</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('professionals.payout-verifications', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'pending' ? 'bg-black text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
                Pending
            </a>
            <a href="{{ route('professionals.payout-verifications', ['status' => 'approved']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'approved' ? 'bg-black text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
                Approved
            </a>
            <a href="{{ route('professionals.payout-verifications', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'rejected' ? 'bg-black text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
                Rejected
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <form action="{{ route('professionals.payout-verifications') }}" method="GET" class="relative w-72">
                <input type="hidden" name="status" value="{{ $status }}">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search pro name or phone..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-black focus:border-black text-sm">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Professional</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Proof</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $req->professional->avatar ?: 'https://i.pravatar.cc/150' }}" class="w-10 h-10 rounded-full border border-gray-100">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $req->professional->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->professional->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $req->type === 'upi' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ strtoupper($req->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php $payout = $req->professional->payout ?? []; @endphp
                            @if($req->type === 'bank')
                                <div class="text-sm font-medium text-gray-900">{{ $payout['bank_name'] ?? '—' }}</div>
                                <div class="text-xs text-gray-500">A/c: {{ $payout['account_number'] ?? '—' }}</div>
                                <div class="text-xs text-gray-400">IFSC: {{ $payout['ifsc'] ?? '—' }}</div>
                            @else
                                <div class="text-sm font-medium text-gray-900">{{ $payout['upi_id'] ?? '—' }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php 
                                $proof = ($req->type === 'bank') ? $req->professional->bank_proof_image : $req->professional->upi_screenshot;
                            @endphp
                            @if($proof)
                                <button onclick="openProofModal('{{ asset($proof) }}')" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> View Proof
                                </button>
                            @else
                                <span class="text-gray-400 text-xs italic">No proof provided</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $req->created_at->format('d M, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($req->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status === 'pending')
                            <div class="flex gap-2">
                                <form action="{{ route('professionals.payout-verifications.approve', $req->id) }}" method="POST" onsubmit="return confirm('Approve this payout method?')">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Approve">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $req->id }})" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Reject">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">No verification requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($requests->hasPages())
        <div class="p-4 border-t border-gray-100 border-gray-100">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 bg-black/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300" id="proofContent">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-900">Verification Proof</h3>
            <button onclick="closeProofModal()" class="p-2 hover:bg-white rounded-full transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 flex justify-center bg-white max-h-[70vh] overflow-auto">
            <img id="proofImage" src="" class="max-w-full rounded-lg shadow-sm border border-gray-100">
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300" id="rejectContent">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Reject Verification</h3>
                <button type="button" onclick="closeRejectModal()" class="p-2 hover:bg-white rounded-full transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                <textarea name="reason" rows="4" required class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-sm" placeholder="Explain why the proof was rejected..."></textarea>
            </div>
            <div class="p-4 bg-gray-50 flex gap-3 justify-end">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-rose-600 text-white text-sm font-bold rounded-xl hover:bg-rose-700 transition-all">Submit Rejection</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openProofModal(url) {
        const modal = document.getElementById('proofModal');
        const content = document.getElementById('proofContent');
        const img = document.getElementById('proofImage');
        img.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeProofModal() {
        const modal = document.getElementById('proofModal');
        const content = document.getElementById('proofContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const content = document.getElementById('rejectContent');
        const form = document.getElementById('rejectForm');
        form.action = `/professionals/payout-verifications/${id}/reject`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        const content = document.getElementById('rejectContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
@endpush
@endsection
