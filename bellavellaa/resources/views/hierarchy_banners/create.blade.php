@extends('layouts.app')
@php $pageTitle = 'Add Service Flow Banner'; @endphp

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hierarchy-banners.index') }}"
            class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Service Flow Banner</h2>
            <p class="text-sm text-gray-400 mt-0.5">Attach a banner to the exact hierarchy entity and placement.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('hierarchy-banners.store') }}" enctype="multipart/form-data"
        class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
        @csrf
        @include('hierarchy_banners._form')

        <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
            <a href="{{ route('hierarchy-banners.index') }}" class="btn btn-secondary">Discard</a>
            <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
                <i data-lucide="check" class="w-4 h-4"></i> Create Banner
            </button>
        </div>
    </form>
</div>
@endsection
