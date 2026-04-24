@extends('layouts.app')
@section('title', 'Tip Settings')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tip Settings</h1>
        <p class="text-sm text-gray-400 mt-1">Manage whether customers can tip professionals during checkout.</p>
    </div>

    {{-- Success / Error Toast --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 font-medium flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('settings.tip.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center">
                        <i data-lucide="heart" class="w-5 h-5 text-pink-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Enable Tip Feature</h2>
                        <p class="text-xs text-gray-400">Show tip section in the customer app</p>
                    </div>
                </div>
                <div class="relative inline-block w-12 h-6">
                    <input type="checkbox" name="tip_enabled" value="1"
                        id="tip_enabled" class="hidden peer" {{ $tipEnabled ? 'checked' : '' }}>
                    <label for="tip_enabled"
                        class="block h-6 rounded-full bg-gray-200 cursor-pointer peer-checked:bg-black transition-colors relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner">
                    </label>
                </div>
            </div>

            <div class="max-w-md mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Default Tip Options</label>
                <input type="text" name="tip_amounts" value="{{ old('tip_amounts', $tipAmounts) }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none transition-all"
                    placeholder="e.g. 50,75,100">
                <p class="text-xs text-gray-400 mt-1.5">Comma-separated values for the tip quick-select buttons in the app.</p>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                class="bg-black text-white px-8 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-md">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Save Tip Settings
            </button>
        </div>
    </form>
</div>
@endsection
