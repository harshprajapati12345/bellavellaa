@extends('layouts.app')
@php $pageTitle = 'Edit Assignment'; @endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Assignment</h2>
            <p class="text-sm text-gray-400 mt-1">Assign or reassign a professional to booking #{{ $booking->id }}</p>
        </div>
        <a href="{{ route('assign.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Booking Details Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Booking Details</h3>
                
                <div class="flex items-center gap-4 mb-6">
                    <img src="{{ $booking->customer?->avatar ?? 'https://i.pravatar.cc/64?img=' . ($booking->id % 50) }}" 
                         class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-50" alt="">
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $booking->customer_display_name }}</p>
                        <p class="text-xs text-gray-400">{{ $booking->city ?? '—' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="scissors" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Service</p>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->service?->name ?? $booking->service_name ?? '—' }}</p>
                            @if($booking->package)
                                <p class="text-xs text-gray-500">{{ $booking->package->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Date & Time</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $booking->date ? \Carbon\Carbon::parse($booking->date)->format('d M Y') : '—' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $booking->slot ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Address</p>
                            <p class="text-sm font-medium text-gray-900 leading-relaxed">{{ $booking->order?->address ?? $booking->address ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-3">Current Professional</p>
                    @if($booking->professional)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50/50 border border-blue-100">
                            <img src="{{ $booking->professional->avatar }}" class="w-8 h-8 rounded-lg object-cover" alt="">
                            <div>
                                <p class="text-sm font-bold text-blue-900">{{ $booking->professional->name }}</p>
                                <p class="text-[10px] text-blue-500 font-semibold uppercase tracking-wider">Assigned</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/50 border border-amber-100 italic text-amber-600 text-xs">
                            Not assigned yet
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Professional Selection -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full min-h-[600px]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Select Professional</h3>
                    <div class="relative w-64">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" id="pro-search" placeholder="Search..." oninput="filterPros()"
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 transition-all">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-3" id="pro-list">
                    @forelse($professionals as $pro)
                        <div class="pro-card p-4 rounded-2xl border-2 {{ $booking->professional_id == $pro->id ? 'border-black bg-gray-50' : 'border-transparent bg-white shadow-sm ring-1 ring-gray-100' }} flex items-center gap-4 hover:shadow-md transition-all cursor-pointer"
                             data-pro-id="{{ $pro->id }}" data-pro-name="{{ $pro->name }}" onclick="selectPro(this)">
                            
                            <div class="pro-checkbox w-5 h-5 rounded-lg border-2 flex items-center justify-center transition-all {{ $booking->professional_id == $pro->id ? 'bg-black border-black' : 'border-gray-200' }}">
                                <i data-lucide="check" class="w-3 h-3 text-white {{ $booking->professional_id == $pro->id ? 'block' : 'hidden' }}"></i>
                            </div>

                            <div class="relative flex-shrink-0">
                                <img src="{{ $pro->avatar }}" class="w-12 h-12 rounded-xl object-cover" alt="">
                                @if($pro->is_online)
                                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white shadow-sm"></span>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $pro->name }}</p>
                                <p class="text-xs text-gray-400">{{ $pro->category }} · {{ $pro->city }}</p>
                            </div>

                            <div class="flex flex-col items-end flex-shrink-0">
                                <div class="flex items-center gap-1 text-emerald-500 mb-0.5">
                                    <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                    <span class="text-xs font-bold">{{ $pro->rating ?? '4.8' }}</span>
                                </div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">{{ $pro->orders ?? 0 }} Orders</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="users" class="w-8 h-8 text-gray-300"></i>
                            </div>
                            <p class="text-sm text-gray-500 font-medium">No active professionals found</p>
                            <p class="text-xs text-gray-400 mt-1">Make sure professionals are set to 'Active' status.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50">
                    <form id="assign-form" action="{{ route('assign.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <input type="hidden" name="professional_id" id="selected-pro-id" value="{{ $booking->professional_id }}">
                        
                        <button type="submit" id="submit-btn" {{ !$booking->professional_id ? 'disabled' : '' }}
                                class="w-full py-4 rounded-2xl bg-black text-white text-sm font-bold hover:bg-gray-800 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 shadow-lg shadow-black/5">
                            Update Assignment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endpush

@push('scripts')
<script>
    let selectedId = {{ $booking->professional_id ?? 'null' }};

    function selectPro(el) {
        const id = el.dataset.proId;
        
        // Deselect all
        document.querySelectorAll('.pro-card').forEach(c => {
            c.classList.remove('border-black', 'bg-gray-50');
            c.classList.add('border-transparent', 'bg-white');
            c.querySelector('.pro-checkbox').classList.remove('bg-black', 'border-black');
            c.querySelector('.pro-checkbox').classList.add('border-gray-200');
            c.querySelector('.pro-checkbox svg').classList.add('hidden');
        });

        // Select chosen
        el.classList.remove('border-transparent', 'bg-white');
        el.classList.add('border-black', 'bg-gray-50');
        el.querySelector('.pro-checkbox').classList.remove('border-gray-200');
        el.querySelector('.pro-checkbox').classList.add('bg-black', 'border-black');
        el.querySelector('.pro-checkbox svg').classList.remove('hidden');

        selectedId = id;
        document.getElementById('selected-pro-id').value = id;
        document.getElementById('submit-btn').disabled = false;
    }

    function filterPros() {
        const q = document.getElementById('pro-search').value.toLowerCase();
        document.querySelectorAll('.pro-card').forEach(c => {
            c.style.display = c.dataset.proName.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    }

    document.getElementById('assign-form').onsubmit = function(e) {
        const proName = document.querySelector(`.pro-card[data-pro-id="${selectedId}"]`).dataset.proName;
        e.preventDefault();
        
        Swal.fire({
            title: 'Confirm Assignment',
            html: `Assign <strong>${proName}</strong> to booking <strong>#{{ $booking->id }}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#000',
            confirmButtonText: 'Yes, Assign'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    };
</script>
@endpush
