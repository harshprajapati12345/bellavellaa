@extends('layouts.app')

@section('content')
<div class="container-fluid py-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🕒 Shift Management</h1>
            <p class="text-sm text-gray-500 mt-1">Configure global working hours for all professionals.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
        <form method="POST" action="{{ route('settings.update') }}" class="p-8">
            @csrf

            <div class="space-y-6">
                <!-- Shift Start Time -->
                <div>
                    <label for="shift_start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                        Shift Start Time
                    </label>
                    <div class="relative">
                        <input type="time" name="shift_start_time" id="shift_start_time" 
                               value="{{ $shiftStart }}" 
                               class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-black focus:ring-0 transition-all text-gray-900 bg-gray-50 focus:bg-white"
                               required>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">The daily time when professionals are expected to start their shift.</p>
                </div>

                <!-- Shift Duration -->
                <div>
                    <label for="shift_duration" class="block text-sm font-semibold text-gray-700 mb-2">
                        Shift Duration (minutes)
                    </label>
                    <div class="relative">
                        <input type="number" name="shift_duration" id="shift_duration" 
                               value="{{ $shiftDuration }}" 
                               class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-black focus:ring-0 transition-all text-gray-900 bg-gray-50 focus:bg-white"
                               min="1" max="1440" required>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Total duration of the shift in minutes (e.g., 480 for 8 hours).</p>
                </div>

                <!-- Withdrawal Cooldown -->
                <div>
                    <label for="withdraw_delay_days" class="block text-sm font-semibold text-gray-700 mb-2">
                        Withdrawal Cooldown (Days)
                    </label>
                    <div class="relative">
                        <input type="number" name="withdraw_delay_days" id="withdraw_delay_days" 
                               value="{{ $withdrawDelayDays }}" 
                               class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-black focus:ring-0 transition-all text-gray-900 bg-gray-50 focus:bg-white"
                               min="1" max="30" required>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Number of days a professional must wait between withdrawals (default 7).</p>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-all shadow-lg shadow-black/10 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Save Shift Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-2xl p-6 max-w-2xl">
        <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-900">How it works?</h4>
                <p class="text-sm text-blue-800 mt-1 leading-relaxed">
                    These settings are applied globally. Professionals will be automatically marked as offline once their shift expires. 
                    The app handles overnight shifts (e.g., if shift starts at 10 PM and ends at 6 AM) automatically based on the duration.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
