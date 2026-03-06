@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rewards Settings</h1>
            <p class="text-gray-600">Configure automated coin rewards for signups and referrals.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('settings.rewards.update') }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($rules as $rule)
                    <div class="p-4 rounded-lg border border-gray-100 bg-gray-50 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $rule->title }}</h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="rules[{{ $rule->id }}][status]" value="1" class="sr-only peer" {{ $rule->status ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">{{ ucfirst($rule->type) }} type reward</p>
                        </div>
                        
                        <div class="relative">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Coins Reward</label>
                            <div class="flex items-center">
                                <input type="number" name="rules[{{ $rule->id }}][coins]" value="{{ $rule->coins }}" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" min="0">
                                <span class="ml-2 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18.1"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">How it works</h3>
        <ul class="space-y-2 text-blue-700">
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Signup Reward:</strong> Automatically given to every new user (Professional or Client) upon account creation.</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Referrer Reward:</strong> Given to the user whose referral code was used, instantly after the new user signs up.</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Referred User Reward:</strong> Bonus coins given to the new user for signing up through a valid referral code.</span>
            </li>
        </ul>
    </div>
</div>
@endsection
