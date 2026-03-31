@extends('layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Services</h2>
        <p class="text-sm text-gray-400 mt-0.5">Level 4 items. Use this screen to manage direct booking vs variant-based booking.</p>
      </div>
      <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
        <i data-lucide="plus" class="w-4 h-4"></i> Add Service
      </a>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <form method="GET" class="grid gap-3 rounded-[2rem] border border-gray-100 bg-white p-5 shadow-sm lg:grid-cols-6">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search service or slug" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none lg:col-span-2">
      <select name="category_id" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
        <option value="">All categories</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
      <select name="service_group_id" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
        <option value="">All groups</option>
        @foreach($groups as $group)
          <option value="{{ $group->id }}" @selected((string) request('service_group_id') === (string) $group->id)>{{ $group->name }}</option>
        @endforeach
      </select>
      <select name="service_type_id" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
        <option value="">All types</option>
        @foreach($types as $type)
          <option value="{{ $type->id }}" @selected((string) request('service_type_id') === (string) $type->id)>{{ $type->name }}</option>
        @endforeach
      </select>
      <div class="flex items-center gap-2">
        <select name="status" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
          <option value="">All status</option>
          <option value="Active" @selected(request('status') === 'Active')>Active</option>
          <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
        </select>
        <button type="submit" class="rounded-xl bg-black px-4 py-2.5 text-sm font-medium text-white">Apply</button>
        <a href="{{ route('services.index') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600">Reset</a>
      </div>
    </form>

    <div class="grid gap-4 md:grid-cols-4">
      <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-widest text-gray-400">Total</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalServices }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-widest text-emerald-500">Active</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ $activeServices }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-widest text-gray-400">Inactive</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ $inactiveServices }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-widest text-amber-500">Top Booked</div>
        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $mostBooked?->name ?? 'N/A' }}</div>
      </div>
    </div>

    <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50/70">
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Service</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Group</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
              <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Variants</th>
              <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Bookable</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
              <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($services as $service)
              @php
                $resolvedCategory = $service->resolved_category;
                $resolvedGroup = $service->resolved_service_group;
                $variantCount = $service->variants->where('status', 'Active')->count();
              @endphp
              <tr>
                <td class="px-6 py-4">
                  <div class="font-semibold text-gray-900">{{ $service->name }}</div>
                  <div class="text-xs text-gray-400">/{{ $service->slug }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $service->serviceType?->name ?? 'Legacy direct service' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $resolvedGroup?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $resolvedCategory?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $variantCount > 0 ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600' }}">{{ $variantCount > 0 ? $variantCount . ' variants' : 'No variants' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $service->canBeBookedDirectly() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $service->canBeBookedDirectly() ? 'Direct' : 'Via variants' }}</span>
                </td>
                <td class="px-6 py-4 text-gray-700">
                  @if($variantCount > 0)
                    <div class="font-semibold">Managed via variants</div>
                    <div class="text-xs text-gray-400">Base {{ number_format($service->display_price, 2) }}</div>
                  @else
                    <div class="font-semibold">{{ number_format($service->display_price, 2) }}</div>
                    @if($service->is_discounted)
                      <div class="text-xs text-gray-400 line-through">{{ number_format($service->original_price, 2) }}</div>
                    @endif
                  @endif
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $service->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $service->status }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('services.edit', $service) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Edit</a>
                    <a href="{{ route('services.edit', $service) }}#variantsSection" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Manage Variants</a>
                    <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('Delete {{ addslashes($service->name) }}?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-16 text-center text-sm text-gray-400">No services found for the selected filters.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
