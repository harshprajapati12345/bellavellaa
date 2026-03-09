@extends('layouts.app')
@php $pageTitle = 'Service Groups'; @endphp

@section('content')
  <div class="flex flex-col gap-6">

    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Service Groups</h2>
        <p class="text-sm text-gray-400 mt-0.5">Manage second-level tiers (Luxe, Prime, Ayurveda) under service categories</p>
      </div>
      <a href="{{ route('service-groups.create') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all shadow-sm shadow-black/10">
        <i data-lucide="plus" class="w-4 h-4"></i> Add Group
      </a>
    </div>

    @if(session('success'))
      <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-medium">
        <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
      </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50/60">
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Group</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sort</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($groups as $group)
              <tr class="hover:bg-gray-50/60 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    @if($group->image)
                      <img src="{{ asset('storage/' . $group->image) }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100" alt="">
                    @else
                      <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                        <i data-lucide="layers" class="w-4 h-4 text-gray-400"></i>
                      </div>
                    @endif
                    <div>
                      <p class="font-semibold text-gray-900">{{ $group->name }}</p>
                      @if($group->tag_label)
                        <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 mt-0.5">{{ $group->tag_label }}</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $group->category?->name ?? '—' }}</td>
                <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $group->slug }}</td>
                <td class="px-6 py-4 text-center text-gray-500">{{ $group->sort_order }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                    {{ $group->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $group->status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('service-groups.edit', $group->id) }}"
                      class="w-8 h-8 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all">
                      <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <form method="POST" action="{{ route('service-groups.destroy', $group->id) }}"
                      onsubmit="return confirm('Delete {{ $group->name }}?')">
                      @csrf @method('DELETE')
                      <button type="submit"
                        class="w-8 h-8 rounded-xl border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                      <i data-lucide="layers" class="w-6 h-6 text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-400">No service groups yet</p>
                    <a href="{{ route('service-groups.create') }}" class="text-sm font-semibold text-black hover:underline">Create your first group</a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
@endsection
