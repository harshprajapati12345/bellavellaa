@extends('layouts.app')
@php $pageTitle = 'Refer & Earn'; @endphp

@section('content')
@php $rules = \App\Models\RewardRule::orderBy('id')->get(); @endphp

<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Refer & Earn</h2>
            <p class="text-sm text-gray-400 mt-0.5">Configure reward points and manage all referrals</p>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2.5 bg-gray-900 text-white text-sm font-medium px-5 py-3 rounded-2xl">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ════════ REWARD POINT CONFIGURATION ════════ --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i data-lucide="settings" class="w-4 h-4 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Reward Point Configuration</p>
                    <p class="text-xs text-gray-400">Admin-controlled. Changes apply to all future rewards.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('settings.rewards.update') }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    @foreach($rules as $rule)
                    <div class="group rounded-xl border border-gray-100 bg-gray-50/60 p-5 hover:border-gray-300 hover:bg-white hover:shadow-sm transition-all">
                        <input type="hidden" name="rules[{{ $rule->id }}][status]" value="0">

                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-sm font-bold text-gray-900">
                                    @if($rule->type === 'referrer')
                                        1st Person
                                    @elseif($rule->type === 'referred_user')
                                        2nd Person
                                    @elseif($rule->type === 'login')
                                        Login Reward
                                    @elseif($rule->type === 'signup')
                                        Signup Reward
                                    @else
                                        {{ $rule->title }}
                                    @endif
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-tight">
                                    @if($rule->type === 'referrer')
                                        Given to the person who shared the code
                                    @elseif($rule->type === 'referred_user')
                                        Given to the person who used the code
                                    @elseif($rule->type === 'login')
                                        Awarded once per day on login
                                    @elseif($rule->type === 'signup')
                                        Awarded on new account creation
                                    @else
                                        {{ $rule->title }}
                                    @endif
                                </p>
                            </div>

                            {{-- Toggle --}}
                            <label class="relative inline-flex items-center cursor-pointer mt-0.5 flex-shrink-0">
                                <input type="checkbox" name="rules[{{ $rule->id }}][status]" value="1"
                                    class="sr-only peer" {{ $rule->status ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-4 after:w-4 after:transition-all peer-checked:bg-gray-900"></div>
                            </label>
                        </div>

                        {{-- Points Input --}}
                        <div class="relative">
                            <input type="number"
                                name="rules[{{ $rule->id }}][coins]"
                                value="{{ $rule->coins }}"
                                min="0" max="99999"
                                class="w-full pl-4 pr-14 py-2.5 bg-white border border-gray-200 rounded-xl text-lg font-bold text-gray-900
                                       focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">pts</span>
                        </div>

                        <p class="text-[10px] text-gray-400 mt-2">
                            @if($rule->max_per_user == 0)
                                No limit per user
                            @else
                                Max {{ $rule->max_per_user }}x per user
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-5 border-t border-gray-100">
                    <p class="text-xs text-gray-400 max-w-sm">Existing wallet balances are not affected. Only future rewards will use these values.</p>
                    <button type="submit"
                        class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Coins Distributed</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_coins']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total given out</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide="coins" class="w-5 h-5 text-gray-600"></i>
            </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Referrals</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($referrals->total()) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">All time</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
            </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Successful</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_referrals']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Completed referrals</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
            </div>
        </div>

        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Top Referrer</p>
                @if($stats['top_referrer'])
                    @php
                        $top     = $stats['top_referrer'];
                        $topUser = $top->referrer_type === 'professional'
                                   ? \App\Models\Professional::find($top->referrer_id)
                                   : \App\Models\Customer::find($top->referrer_id);
                    @endphp
                    <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $topUser->name ?? 'User #'.$top->referrer_id }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $top->count }} referrals</p>
                @else
                    <p class="text-2xl font-bold text-gray-900">—</p>
                    <p class="text-xs text-gray-400 mt-0.5">None yet</p>
                @endif
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
            </div>
        </div>
    </div>

    {{-- Referrals Table --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">1st Person (Referrer)</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">2nd Person (Referred)</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Reward</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referrals as $ref)
                    <tr class="table-row border-b border-gray-50 hover:bg-gray-50/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr($ref->referrer->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $ref->referrer->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400 capitalize">{{ $ref->referrer_type }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($ref->referred)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ strtoupper(substr($ref->referred->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $ref->referred->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-400 capitalize">{{ $ref->referred_type }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">Not joined yet</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                {{ $ref->referred_type === 'professional' ? 'bg-gray-100 text-gray-700' : 'bg-gray-50 text-gray-600' }}">
                                {{ ucfirst($ref->referred_type ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $ref->referred_phone }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($ref->reward_amount) }} pts</p>
                            <p class="text-xs text-gray-400 capitalize">{{ $ref->reward_type }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusClass = match($ref->status) {
                                    'success' => 'bg-emerald-50 text-emerald-600',
                                    'pending'  => 'bg-amber-50 text-amber-600',
                                    'expired'  => 'bg-red-50 text-red-500',
                                    default    => 'bg-gray-50 text-gray-500',
                                };
                                $dotClass = match($ref->status) {
                                    'success' => 'bg-emerald-400',
                                    'pending'  => 'bg-amber-400',
                                    default    => 'bg-red-400',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ ucfirst($ref->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-400">{{ $ref->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('referrals.toggle-status', $ref->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $ref->status === 'success' ? 'pending' : 'success' }}">
                                    <button type="submit"
                                        title="{{ $ref->status === 'success' ? 'Mark as Pending' : 'Mark as Successful' }}"
                                        class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                                        <i data-lucide="{{ $ref->status === 'success' ? 'clock' : 'check-circle' }}" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                                <form action="{{ route('referrals.toggle-status', $ref->id) }}" method="POST"
                                    onsubmit="return confirm('Expire this referral?')">
                                    @csrf
                                    <input type="hidden" name="status" value="expired">
                                    <button type="submit" title="Mark as Expired"
                                        class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($referrals->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                <i data-lucide="gift" class="w-8 h-8 text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium">No referrals yet</p>
            <p class="text-gray-400 text-sm mt-1">Referrals will appear here once users start sharing codes</p>
        </div>
        @endif

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $referrals->links() }}
        </div>
    </div>

</div>
@endsection
