@extends('layouts.app')
@php $pageTitle = 'Review Verification'; @endphp

@section('content')
@php
    $statusColor = match($req['status']) { 'Approved', 'Verified' => 'emerald', 'Pending' => 'amber', default => 'red' };
    $status = $req['status'];
    $infoCards = [
        ['label' => 'Email', 'value' => $req['email'], 'icon' => 'mail'],
        ['label' => 'Phone', 'value' => $req['phone'], 'icon' => 'phone'],
        ['label' => 'Gender', 'value' => $req['gender'], 'icon' => 'venus'],
        ['label' => 'DOB', 'value' => $req['dob'] ? \Carbon\Carbon::parse($req['dob'])->format('d/m/Y') : '-', 'icon' => 'calendar-days'],
        ['label' => 'Experience', 'value' => $req['experience'] ?: '-', 'icon' => 'briefcase'],
        ['label' => 'State', 'value' => $req['state'], 'icon' => 'map'],
        ['label' => 'City', 'value' => $req['city'], 'icon' => 'building-2'],
        ['label' => 'Pincode', 'value' => $req['pincode'], 'icon' => 'map-pinned'],
        ['label' => 'Aadhaar', 'value' => $req['aadhaar'], 'icon' => 'badge-check'],
        ['label' => 'PAN', 'value' => $req['pan'], 'icon' => 'credit-card'],
    ];
    $proofs = [
        ['label' => 'Aadhaar Front', 'url' => $req['aadhaar_front']],
        ['label' => 'Aadhaar Back', 'url' => $req['aadhaar_back']],
        ['label' => 'PAN Photo', 'url' => $req['pan_img']],
        ['label' => 'Certificate', 'url' => $req['certificate_img'] ?? null],
        ['label' => 'Light Bill', 'url' => $req['light_bill'] ?? null],
        ['label' => 'Live Selfie', 'url' => $req['selfie'] ?? null],
    ];
@endphp

<div class="max-w-7xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('professionals.verification') }}"
            class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Review Verification</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Case #{{ str_pad($req['id'], 6, '0', STR_PAD_LEFT) }}</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-{{ $statusColor }}-600">{{ $status }} Submission</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_2px_16px_rgba(0,0,0,0.04)] p-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex items-start gap-5 min-w-0">
                <img src="{{ $req['avatar'] }}" class="w-24 h-24 rounded-[1.5rem] object-cover bg-gray-100 ring-4 ring-gray-50" alt="">
                <div class="min-w-0">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $req['name'] }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Submitted on {{ \Carbon\Carbon::parse($req['submitted'])->format('F d, Y \a\t h:i A') }}
                    </p>
                    <div class="mt-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-2">Address</p>
                        <p class="text-sm font-medium text-gray-900 leading-6">
                            {{ $req['address'] ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($infoCards as $item)
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/80 px-4 py-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 rounded-xl bg-white shadow-sm flex items-center justify-center">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 text-gray-700"></i>
                            </div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">{{ $item['label'] }}</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 break-words">{{ $item['value'] ?: '-' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <div class="rounded-[1.75rem] border border-gray-100 p-6 bg-gray-50/60">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-4">Skills</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($req['skills'] as $skill)
                        <span class="px-3 py-2 rounded-xl bg-white border border-gray-200 text-sm font-medium text-gray-800">{{ $skill }}</span>
                    @empty
                        <span class="text-sm text-gray-500">No skills submitted.</span>
                    @endforelse
                </div>
            </div>
            <div class="rounded-[1.75rem] border border-gray-100 p-6 bg-gray-50/60">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-4">Languages Known</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($req['languages'] as $language)
                        <span class="px-3 py-2 rounded-xl bg-white border border-gray-200 text-sm font-medium text-gray-800">{{ $language }}</span>
                    @empty
                        <span class="text-sm text-gray-500">No languages submitted.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_2px_16px_rgba(0,0,0,0.04)] p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center text-sm font-bold">2</div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Verification Images</h3>
                <p class="text-sm text-gray-500">All uploaded documents and proof images from signup.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($proofs as $proof)
                <div class="rounded-[1.5rem] border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $proof['label'] }}</p>
                        @if($proof['url'])
                            <button type="button" onclick="previewImage('{{ $proof['url'] }}')" class="text-xs font-semibold text-gray-500 hover:text-gray-900">Preview</button>
                        @endif
                    </div>
                    @if($proof['url'])
                        <div class="rounded-[1.25rem] overflow-hidden bg-white border border-gray-200 cursor-zoom-in" onclick="previewImage('{{ $proof['url'] }}')">
                            <img src="{{ $proof['url'] }}" alt="{{ $proof['label'] }}" class="w-full h-64 object-cover">
                        </div>
                    @else
                        <div class="rounded-[1.25rem] h-64 border border-dashed border-gray-300 bg-white flex items-center justify-center text-sm font-medium text-gray-400">
                            Not uploaded
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="sticky bottom-6 z-30 bg-white/85 backdrop-blur-xl rounded-[2rem] border border-gray-100 shadow-xl px-8 py-5 flex items-center justify-end gap-3">
        <a href="{{ route('professionals.verification') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
        @if($status === 'Pending')
            <button onclick="requestReupload()" class="px-5 py-2.5 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-all text-sm font-medium flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Request Changes
            </button>
            <button onclick="rejectDoc()" class="px-5 py-2.5 rounded-xl bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-all text-sm font-medium flex items-center gap-2">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Reject
            </button>
            <button onclick="approveDoc()" class="px-8 py-2.5 rounded-xl bg-black text-white text-sm font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/10 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Approve
            </button>
        @else
            <p class="text-sm font-semibold text-gray-400 mr-4 flex items-center gap-2">
                <i data-lucide="lock" class="w-4 h-4"></i> Status: {{ $status }}
            </p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(src) {
        if (!src) return;

        Swal.fire({
            imageUrl: src,
            imageAlt: 'Document Preview',
            width: 'auto',
            showConfirmButton: false,
            showCloseButton: true,
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.85)',
            customClass: {
                popup: 'rounded-3xl border-0 bg-transparent',
                image: 'rounded-2xl shadow-2xl ring-4 ring-white/10'
            }
        });
    }

    function approveDoc() {
        Swal.fire({
            title: 'Approve Submission?',
            text: 'The professional will be notified and can start accepting orders.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Yes, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('professionals.verification.approve', $req['id']) }}";
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function rejectDoc() {
        Swal.fire({
            title: 'Reject Application',
            input: 'textarea',
            inputPlaceholder: 'Please provide a clear reason for rejection...',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Confirm Rejection'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('professionals.verification.reject', $req['id']) }}";
                form.innerHTML = `@csrf <input type="hidden" name="reason" value="${result.value ?? ''}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function requestReupload() {
        Swal.fire({
            title: 'Request Changes',
            text: 'Ask the professional to re-upload specific documents?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#000',
            confirmButtonText: 'Yes, Send Request'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('professionals.verification.request-changes', $req['id']) }}";
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
