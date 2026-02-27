@extends('layouts.app')
@php $pageTitle = 'Review Verification'; @endphp

@section('content')
@php
$statusColor = match($req['status']) { 'Approved' => 'emerald', 'Verified' => 'emerald', 'Pending' => 'amber', default => 'red' };
$status = $req['status'];
@endphp

<div class="max-w-6xl mx-auto flex flex-col gap-8">

  <!-- Page Header -->
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

  <!-- Applicant Profile Card -->
  <div class="bg-white rounded-[2.5rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col md:flex-row items-center md:items-start gap-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-full opacity-50"></div>
    <img src="{{ $req['avatar'] }}" class="w-28 h-28 rounded-[2rem] object-cover ring-8 ring-gray-50 bg-gray-100 shadow-sm relative z-10" alt="">
    <div class="flex-1 text-center md:text-left space-y-5 relative z-10">
      <div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $req['name'] }}</h3>
        <p class="text-sm text-gray-400 mt-1 flex items-center justify-center md:justify-start gap-1.5 font-medium">
          <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Submitted on {{ \Carbon\Carbon::parse($req['submitted'])->format('F d, Y \a\t h:i A') }}
        </p>
      </div>
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
        <div class="px-5 py-3 rounded-2xl bg-gray-50/80 border border-gray-100 flex items-center gap-4">
          <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm"><i data-lucide="mail" class="w-4 h-4 text-emerald-500"></i></div>
          <div class="text-left leading-none"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Email</p><p class="text-sm font-semibold text-gray-900">{{ $req['email'] }}</p></div>
        </div>
        <div class="px-5 py-3 rounded-2xl bg-gray-50/80 border border-gray-100 flex items-center gap-4">
          <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm"><i data-lucide="phone" class="w-4 h-4 text-blue-500"></i></div>
          <div class="text-left leading-none"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Phone</p><p class="text-sm font-semibold text-gray-900">{{ $req['phone'] }}</p></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Documents Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Identity Documents -->
    <div class="space-y-6">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center text-xs font-bold">1</div>
        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-[0.2em]">Identity Documents</h3>
        <div class="flex-1 h-px bg-gray-100 ml-2"></div>
      </div>
      
      <!-- Aadhaar -->
      <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center"><i data-lucide="credit-card" class="w-6 h-6 text-blue-600"></i></div>
            <div><p class="font-bold text-gray-900">Aadhaar Card</p><p class="text-xs text-gray-400 font-medium">Government identity proof</p></div>
          </div>
          <span class="font-mono text-sm font-bold bg-gray-50 border border-gray-100 px-4 py-2 rounded-xl text-gray-600 tracking-widest">{{ $req['aadhaar'] }}</span>
        </div>
        <div class="grid grid-cols-2 gap-6">
          <div class="group">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Card Front</p>
            <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-44 relative ring-1 ring-gray-100 ring-offset-4 ring-offset-white cursor-zoom-in" onclick="previewImage('{{ $req['aadhaar_front'] }}')">
              <img src="{{ $req['aadhaar_front'] }}" class="w-full h-full object-cover">
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white w-8 h-8"></i></div>
            </div>
          </div>
          <div class="group">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Card Back</p>
            <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-44 relative ring-1 ring-gray-100 ring-offset-4 ring-offset-white cursor-zoom-in" onclick="previewImage('{{ $req['aadhaar_back'] }}')">
              <img src="{{ $req['aadhaar_back'] }}" class="w-full h-full object-cover">
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white w-8 h-8"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- PAN Card -->
      <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center"><i data-lucide="file-text" class="w-6 h-6 text-amber-600"></i></div>
            <div><p class="font-bold text-gray-900">PAN Card</p><p class="text-xs text-gray-400 font-medium">Income tax identity proof</p></div>
          </div>
          <span class="font-mono text-sm font-bold bg-gray-50 border border-gray-100 px-4 py-2 rounded-xl text-gray-600 tracking-widest">{{ $req['pan'] }}</span>
        </div>
        <div class="group">
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Card Front</p>
          <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-56 relative ring-1 ring-gray-100 ring-offset-4 ring-offset-white cursor-zoom-in" onclick="previewImage('{{ $req['pan_img'] }}')">
            <img src="{{ $req['pan_img'] }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white w-8 h-8"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Face Match Verification -->
    <div class="space-y-6">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center text-xs font-bold">2</div>
        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-[0.2em]">Identity Verification</h3>
        <div class="flex-1 h-px bg-gray-100 ml-2"></div>
      </div>

      <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 h-full flex flex-col">
        <div class="mb-8 bg-violet-50/50 p-5 rounded-[1.5rem] border border-violet-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm"><i data-lucide="scan-face" class="w-6 h-6 text-violet-600"></i></div>
          <div>
            <p class="text-sm font-bold text-violet-900 tracking-tight">Facial Match Test</p>
            <p class="text-xs text-violet-600/80 font-medium mt-0.5">Visually compare the live selfie with the ID card photo.</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-8 flex-1">
          <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
              <span class="text-[10px] font-bold text-gray-900 uppercase tracking-widest">Selfie Check</span>
              <span class="text-[9px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-lg uppercase tracking-wider">Live</span>
            </div>
            <div class="doc-card rounded-[2rem] overflow-hidden bg-gray-50 aspect-[3/4] relative shadow-inner ring-1 ring-gray-100/50 cursor-zoom-in" onclick="previewImage('{{ $req['selfie'] }}')">
              <img src="{{ $req['selfie'] }}" class="w-full h-full object-cover">
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all pointer-events-none"></div>
            </div>
          </div>
          <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
              <span class="text-[10px] font-bold text-gray-900 uppercase tracking-widest">Photo Crop</span>
              <span class="text-[9px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-lg uppercase tracking-wider">From ID</span>
            </div>
            <div class="doc-card rounded-[2rem] overflow-hidden bg-gray-50 aspect-[3/4] relative shadow-inner ring-1 ring-gray-100/50 cursor-zoom-in" onclick="previewImage('{{ $req['aadhaar_front'] }}')">
              <img src="{{ $req['aadhaar_front'] }}" class="w-full h-full object-cover scale-[1.2] translate-y-[-10%]">
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all pointer-events-none"></div>
            </div>
          </div>
        </div>

        <div class="mt-10 pt-8 border-t border-gray-50">
          <p class="text-[10px] font-bold text-gray-400 mb-5 text-center uppercase tracking-[0.2em]">Verification Checklist</p>
          <div class="space-y-3">
            @foreach([
              'Facial features match (eyes, nose, jawline)',
              'Identity document text is clearly visible',
              'No blurred edges or potential edits detected'
            ] as $check)
            <label class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all cursor-pointer border border-transparent hover:border-gray-100 group">
              <div class="relative flex items-center justify-center">
                <input type="checkbox" class="w-6 h-6 rounded-lg border-gray-200 text-black focus:ring-black appearance-none border transition-all checked:bg-black checked:border-black peer">
                <i data-lucide="check" class="w-3.5 h-3.5 text-white absolute transition-opacity opacity-0 peer-checked:opacity-100"></i>
              </div>
              <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">{{ $check }}</span>
            </label>
            @endforeach
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Sticky Action Bar -->
  <div class="sticky bottom-6 z-30 bg-white/80 backdrop-blur-xl rounded-[2rem] border border-gray-100 shadow-xl px-8 py-5 flex items-center justify-end gap-3 mt-4">
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

@push('styles')
<style>
  .doc-card { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
  .doc-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); }
</style>
@endpush

@push('scripts')
<script>
  function previewImage(src) {
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
      text: "The professional will be notified and can start accepting orders.",
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
        form.innerHTML = `@csrf <input type="hidden" name="reason" value="${result.value}">`;
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
