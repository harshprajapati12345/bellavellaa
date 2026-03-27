@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🏆 Top 3 Professionals</h1>
            <p class="text-gray-500">Recognizing our top performers by completed jobs</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($topProfessionals as $pro)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center transition-all hover:shadow-md">
            <div class="relative mb-4">
                <img src="{{ $pro['image'] }}" 
                     alt="{{ $pro['name'] }}"
                     class="w-24 h-24 rounded-full object-cover border-4 border-gray-50 shadow-sm">
                
                <div class="absolute -top-2 -right-2 bg-white rounded-full p-2 shadow-sm border border-gray-50">
                    <span class="text-2xl">
                        @if($pro['rank'] == 1) 🥇
                        @elseif($pro['rank'] == 2) 🥈
                        @else 🥉
                        @endif
                    </span>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $pro['name'] }}</h3>
            <p class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full mb-4">
                {{ $pro['role'] }}
            </p>

            <div class="w-full flex items-center justify-around border-t border-gray-50 pt-4">
                <div class="text-center">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Rank</p>
                    <p class="text-xl font-bold text-gray-900">#{{ $pro['rank'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Completed Jobs</p>
                    <div class="flex items-center justify-center gap-1">
                        <span class="text-xl font-bold text-gray-900">{{ $pro['completed_jobs_count'] }}</span>
                        <span class="text-orange-400">⭐</span>
                    </div>
                </div>
            </div>

            @if($pro['rank'] == 1)
            <div class="mt-6 w-full py-3 bg-gradient-to-r from-orange-400 to-amber-500 rounded-xl text-white font-bold shadow-lg shadow-orange-200">
                Top Performer
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
