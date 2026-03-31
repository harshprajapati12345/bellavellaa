@extends('layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Service Types</h2>
        <p class="text-sm text-gray-400 mt-0.5">Level 3 drill-down under each service group.</p>
      </div>
      <a href="{{ route('service-types.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i> Add Service Type
      </a>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="GET" class="grid gap-3 rounded-[2rem] border border-gray-100 bg-white p-5 shadow-sm md:grid-cols-5">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search type or slug" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
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
      <select name="status" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
        <option value="">All status</option>
        <option value="Active" @selected(request('status') === 'Active')>Active</option>
        <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
      </select>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-xl bg-black px-4 py-2.5 text-sm font-medium text-white">Apply</button>
        <a href="{{ route('service-types.index') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600">Reset</a>
      </div>
    </form>

    <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50/70">
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Group</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
              <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Services</th>
              <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($types as $type)
              <tr>
                <td class="px-6 py-4">
                  <div class="font-semibold text-gray-900">{{ $type->name }}</div>
                  <div class="text-xs text-gray-400">/{{ $type->slug }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $type->serviceGroup?->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $type->serviceGroup?->category?->name }}</td>
                <td class="px-6 py-4 text-center text-gray-600">{{ $type->services_count }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $type->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $type->status }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('services.index', ['service_type_id' => $type->id]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Manage Services</a>
                    <a href="{{ route('service-types.edit', $type) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Edit</a>
                    <form method="POST" action="{{ route('service-types.destroy', $type) }}">
                      @csrf @method('DELETE')
                      <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-400">No service types found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="border-t border-gray-100 px-6 py-4">
        {{ $types->links() }}
      </div>
    </div>
  </div>
@endsection
