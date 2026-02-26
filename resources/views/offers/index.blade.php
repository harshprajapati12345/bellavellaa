@extends('layouts.app')
@php $pageTitle = 'Offers'; @endphp

@section('content')
@php
$offers = $offers ?? collect();
$total      = $offers->count();
$active     = $offers->where('status', 'Active')->count();
$totalUsage = $offers->sum('usage');
@endphp

    <div class="flex flex-col gap-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Promotional Offers</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage discounts, coupons and seasonal offers</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" placeholder="Search offers…"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
          </div>
          <a href="{{ route('offers.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Offer
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900">{{ $total }}</p><p class="text-xs text-gray-400 mt-0.5">Active & Inactive</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="tag" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Live Now</p><p class="text-3xl font-bold text-gray-900">{{ $active }}</p><p class="text-xs text-gray-400 mt-0.5">Current Offers</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">Total Usage</p><p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsage) }}</p><p class="text-xs text-gray-400 mt-0.5">All time claims</p></div>
          <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0"><i data-lucide="ticket" class="w-5 h-5 text-blue-500"></i></div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">ID</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Offer Details</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Code</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Discount</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Usage</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Expiry</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-4 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($offers as $o)
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $o->id }}"
                  data-name="{{ strtolower($o->title ?? $o->name ?? '') }}"
                  data-display-name="{{ $o->title ?? $o->name }}"
                  data-description="{{ $o->description ?? '' }}"
                  data-code="{{ $o->code ?? '—' }}"
                  data-type="{{ $o->type ?? 'Percentage' }}"
                  data-discount="{{ $o->discount_display ?? ($o->discount.'%') }}"
                  data-usage="{{ $o->usage ?? 0 }}"
                  data-max-usage="{{ $o->max_usage ?? 'Unlimited' }}"
                  data-min-spend="{{ $o->min_spend ?? 0 }}"
                  data-start="{{ $o->start_date ? \Carbon\Carbon::parse($o->start_date)->format('d M Y') : '—' }}"
                  data-end="{{ $o->end_date ? \Carbon\Carbon::parse($o->end_date)->format('d M Y') : '—' }}"
                  data-status="{{ $o->status ?? 'Active' }}"
                  data-created="{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y') }}">
                <td class="px-6 py-4 text-sm text-gray-400 font-medium">#{{ $o->id }}</td>
                <td class="px-4 py-4"><span class="text-sm font-semibold text-gray-900">{{ $o->title ?? $o->name }}</span></td>
                <td class="px-4 py-4">
                  <span class="text-xs font-mono bg-gray-100 px-3 py-1 rounded-lg text-gray-600 font-bold border border-gray-200">{{ $o->code ?? '—' }}</span>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900">{{ $o->discount_display ?? ($o->discount.'%') }}</span>
                    <span class="text-[10px] text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">{{ $o->type ?? 'Percentage' }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-center"><span class="text-sm font-medium text-gray-700">{{ $o->usage ?? 0 }}</span></td>
                <td class="px-4 py-4 text-sm text-gray-500">{{ $o->end_date ? \Carbon\Carbon::parse($o->end_date)->format('d M Y') : '—' }}</td>
                <td class="px-4 py-4">
                  <label class="toggle-switch">
                    <input type="checkbox" {{ ($o->status ?? 'Active') === 'Active' ? 'checked' : '' }} onchange="toggleStatus({{ $o->id }}, this)">
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td class="px-4 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button class="view-btn w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" data-id="{{ $o->id }}" data-type="offers" title="View Details">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <a href="{{ route('offers.edit', $o->id) }}" title="Edit"
                      class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-colors">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    <button onclick="deleteOffer({{ $o->id }}, '{{ addslashes($o->title ?? $o->name ?? '') }}')" title="Delete"
                      class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>

<!-- ── VIEW DRAWER ──────────────────────────────────────────────────────── -->
@endsection

@push('styles')
<style>
  .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
  .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
</style>
@endpush

@push('scripts')
<script>
  function deleteOffer(id, name) {
    Swal.fire({
      title: `Delete ${name}?`,
      text: 'This action cannot be undone and will immediately disable the offer code.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete it'
    }).then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/offers/${id}`;
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endpush
