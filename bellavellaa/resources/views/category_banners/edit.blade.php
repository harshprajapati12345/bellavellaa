@extends('layouts.app')
@php $pageTitle = 'Edit Category Banner'; @endphp

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('category-banners.index') }}"
            class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Banner</h2>
            <p class="text-sm text-gray-400 mt-0.5">Update banner details for {{ $categoryBanner->category->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">
            @foreach($errors->all() as $e)
            <li><i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('category-banners.update', $categoryBanner->id) }}" enctype="multipart/form-data"
        class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
        @csrf
        @method('PUT')
        
        @include('category_banners._form')

        <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
            <a href="{{ route('category-banners.index') }}" class="btn btn-secondary">Discard Changes</a>
            <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
                <i data-lucide="check" class="w-4 h-4"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('img-preview').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
