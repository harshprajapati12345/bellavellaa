@extends('layouts.app')
@php $pageTitle = 'Professionals'; @endphp

@section('content')
@php
$professionals = $professionals ?? collect();
$total     = $professionals->count();
$verified  = $professionals->where('verification', 'Verified')->count();
$pending   = $professionals->where('verification', 'Pending')->count();
$suspended = $professionals->where('status', 'Suspended')->count();
@endphp

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Professionals Overview</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all registered beauty professionals</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input id="pro-search" type="text" placeholder="Name / Phone / City…" oninput="applyFilters()"
              class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
          </div>
          <a href="{{ route('professionals.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Professional
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total</p><p class="text-3xl font-bold text-gray-900">{{ $total }}</p><p class="text-xs text-gray-400 mt-0.5">Professionals</p></div>
          <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Verified</p><p class="text-3xl font-bold text-gray-900">{{ $verified }}</p><p class="text-xs text-gray-400 mt-0.5">Approved</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="badge-check" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p><p class="text-3xl font-bold text-gray-900">{{ $pending }}</p><p class="text-xs text-gray-400 mt-0.5">Awaiting review</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="clock" class="w-5 h-5 text-amber-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Suspended</p><p class="text-3xl font-bold text-gray-900">{{ $suspended }}</p><p class="text-xs text-gray-400 mt-0.5">Restricted</p></div>
          <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0"><i data-lucide="ban" class="w-5 h-5 text-red-400"></i></div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex flex-wrap items-center gap-1.5">
        @foreach(['all'=>'All','verified'=>'Verified','pending'=>'Pending','rejected'=>'Rejected','active'=>'Active','suspended'=>'Suspended'] as $k=>$v)
        <button onclick="setTab('{{ $k }}')" id="tab-{{ $k }}"
          class="filter-tab text-sm font-medium px-4 py-2 {{ $k==='all'?'active':'' }}">
          {{ $v }}
        </button>
        @endforeach
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1000px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">City</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Verification</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Orders</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Earnings</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody id="pro-tbody">
              @foreach($professionals as $pro)
              @php
                $vClass = match($pro->verification ?? '') { 'Verified' => 'bg-emerald-50 text-emerald-600', 'Pending' => 'bg-amber-50 text-amber-600', default => 'bg-red-50 text-red-500' };
                $vIcon = match($pro->verification ?? '') { 'Verified' => 'badge-check', 'Pending' => 'clock', default => 'x-circle' };
                $services = is_array($pro->services) ? $pro->services : (is_string($pro->services) ? json_decode($pro->services, true) ?? [] : []);
              @endphp
              <tr class="table-row border-b border-gray-50"
                  data-id="{{ $pro->id }}"
                  data-name="{{ strtolower($pro->name) }}"
                  data-display-name="{{ $pro->name }}"
                  data-phone="{{ $pro->phone }}"
                  data-city="{{ strtolower($pro->city ?? '') }}"
                  data-display-city="{{ $pro->city ?? '—' }}"
                  data-status="{{ strtolower($pro->status ?? 'active') }}"
                  data-verification="{{ strtolower($pro->verification ?? 'pending') }}"
                  data-avatar="{{ $pro->avatar ?? $pro->image ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80' }}"
                  data-category="{{ $pro->category ?? 'Prime' }}"
                  data-orders="{{ $pro->orders ?? 0 }}"
                  data-earnings="{{ $pro->earnings ?? 0 }}"
                  data-rating="{{ $pro->rating ?? 0 }}"
                  data-joined="{{ $pro->joined ?? $pro->created_at }}"
                  data-experience="{{ $pro->experience ?? '—' }}"
                  data-commission="{{ $pro->commission ?? 0 }}"
                  data-services='@json($services)'
                  data-docs="{{ $pro->docs ? 'true' : 'false' }}">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="{{ $pro->avatar ?? $pro->image ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80' }}" class="w-10 h-10 rounded-full object-cover avatar-ring flex-shrink-0" alt="{{ $pro->name }}">
                    <div>
                      <div class="flex items-center gap-1.5">
                        <p class="text-sm font-semibold text-gray-900">{{ $pro->name }}</p>
                        @if(($pro->verification ?? '') === 'Verified')<i data-lucide="badge-check" class="w-3.5 h-3.5 text-blue-500 fill-blue-50"></i>@endif
                      </div>
                      <p class="text-xs text-gray-400">{{ $pro->experience ?? '—' }} exp.</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  @if(($pro->category ?? '') === 'Luxe')
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-50 text-violet-600">Luxe</span>
                  @else
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">Prime</span>
                  @endif
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $pro->phone }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5 text-sm text-gray-600">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>{{ $pro->city ?? '—' }}
                  </div>
                </td>
                <td class="px-5 py-4">
                  @if(($pro->status ?? 'Active') === 'Active')
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Active</span>
                  @else
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-500"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Suspended</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $vClass }}">
                    <i data-lucide="{{ $vIcon }}" class="w-3 h-3"></i>{{ $pro->verification ?? 'Pending' }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm font-medium text-gray-700">{{ number_format($pro->orders ?? 0) }}</td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($pro->earnings ?? 0) }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-1.5">
                      <button type="button" class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center" data-id="{{ $pro->id }}" data-type="professionals" title="View Details">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      </button>
                    <a href="{{ route('professionals.edit', $pro->id) }}" title="Edit"
                      class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <button onclick="toggleSuspend({{ $pro->id }}, '{{ $pro->status }}')" title="{{ ($pro->status ?? 'Active') === 'Active' ? 'Suspend' : 'Activate' }}"
                      class="w-8 h-8 rounded-lg border {{ ($pro->status ?? 'Active') === 'Active' ? 'border-amber-100 text-amber-500 hover:bg-amber-500' : 'border-emerald-100 text-emerald-500 hover:bg-emerald-500' }} hover:text-white transition-all flex items-center justify-center">
                      <i data-lucide="{{ ($pro->status ?? 'Active') === 'Active' ? 'pause-circle' : 'play-circle' }}" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick="deletePro({{ $pro->id }})" title="Delete"
                      class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                      <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <i data-lucide="users-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No professionals found</p>
          <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>
      </div>

    </div>

  </div>
@endsection

@push('scripts')
<script>
  function toggleSuspend(id, currentStatus) {
    const action = currentStatus === 'Active' ? 'Suspend' : 'Activate';
    Swal.fire({ title: `${action} Professional?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: `Yes, ${action}` })
      .then(r => { if (r.isConfirmed) Swal.fire({ title: `${action}d!`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); });
  }

  function changeCommission(id, current) {
    Swal.fire({ title: 'Change Commission', input: 'number', inputValue: current, inputAttributes: { min: 0, max: 50, step: 1 }, showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Update', inputLabel: 'Commission %' })
      .then(r => { if (r.isConfirmed) Swal.fire({ title: 'Updated!', text: `Commission set to ${r.value}%`, icon: 'success', confirmButtonColor: '#000', timer: 1800, showConfirmButton: false }); });
  }

  function deletePro(id) {
    Swal.fire({
      title: 'Remove Professional?',
      text: 'This will permanently remove them from the system.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete'
    }).then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('professionals.destroy', '') }}/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#pro-tbody tr.table-row')); renderPage(); })();
</script>
@endpush
