@extends('layouts.app')

@section('title', 'Checkout Discount Settings · Bellavella Admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Checkout Discount Settings</h1>
            <p class="text-sm text-gray-500 mt-2">Configure platform-level discounts that encourage specific customer behaviors like online payments and wallet usage.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-100 px-6 py-4 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-emerald-900">Success!</p>
                    <p class="text-xs text-emerald-700/80">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @php
            $onlineDiscountEnabled = \App\Models\Setting::getBool('checkout_online_discount_enabled', false);
            $onlineDiscountPercent = \App\Models\Setting::getInt('checkout_online_discount_percent', 0);
            $walletDiscountEnabled = \App\Models\Setting::getBool('checkout_wallet_discount_enabled', false);
            $walletDiscountPercent = \App\Models\Setting::getInt('checkout_wallet_discount_percent', 0);
        @endphp

        <form id="checkout-discount-settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 gap-8">
                <!-- Online Discount Card -->
                <div class="group bg-white rounded-3xl p-8 shadow-[0_2px_24px_rgba(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_40px_rgba(0,0,0,0.06)] transition-all duration-300">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600 transition-transform group-hover:scale-110 duration-300">
                                <i data-lucide="credit-card" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Online Payment Incentive</h3>
                                <p class="text-xs text-gray-400 mt-0.5 font-medium uppercase tracking-wider">Applied on online checkout</p>
                            </div>
                        </div>
                        <div class="relative inline-block w-14 h-7 transition duration-200 ease-in-out">
                            <input type="hidden" name="settings[checkout_online_discount_enabled]" value="0">
                            <input type="checkbox" name="settings[checkout_online_discount_enabled]" value="1" 
                                id="online_enabled" class="hidden peer" {{ $onlineDiscountEnabled ? 'checked' : '' }}>
                            <label for="online_enabled" 
                                class="block overflow-hidden h-7 rounded-full bg-gray-100 cursor-pointer peer-checked:bg-black transition-colors after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-all peer-checked:after:translate-x-7 shadow-inner">
                            </label>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="max-w-xs">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">Incentive Percentage (%)</label>
                            <div class="relative group/input">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 pointer-events-none group-focus-within/input:text-black transition-colors">%</span>
                                <input type="number" min="0" max="100" name="settings[checkout_online_discount_percent]" 
                                    value="{{ old('settings.checkout_online_discount_percent', $onlineDiscountPercent) }}" 
                                    data-discount-value="online"
                                    class="discount-input w-full rounded-2xl border-2 border-gray-100 px-5 py-4 text-lg font-bold focus:border-black focus:outline-none disabled:bg-gray-50/50 disabled:text-gray-300 disabled:border-gray-50 transition-all"
                                    placeholder="0"
                                    {{ !$onlineDiscountEnabled ? 'disabled' : '' }}>
                            </div>
                            @error('settings.checkout_online_discount_percent')
                                <p class="text-xs text-red-500 mt-2 font-medium flex items-center gap-1.5">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="p-6 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                                <div class="space-y-2">
                                    <p class="text-xs leading-relaxed text-gray-500">
                                        This discount is applied on the final checkout total <span class="text-black font-semibold">after applying any coupons</span>. This encourages digital payments and reduces Cash on Delivery risks.
                                    </p>
                                    <div class="pt-2">
                                        <p class="text-[11px] font-bold text-black flex items-center gap-2" data-preview="online"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Discount Card -->
                <div class="group bg-white rounded-3xl p-8 shadow-[0_2px_24px_rgba(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_40px_rgba(0,0,0,0.06)] transition-all duration-300">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-violet-500/10 flex items-center justify-center text-violet-600 transition-transform group-hover:scale-110 duration-300">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Wallet Usage Reward</h3>
                                <p class="text-xs text-gray-400 mt-0.5 font-medium uppercase tracking-wider">Applied for wallet redemptions</p>
                            </div>
                        </div>
                        <div class="relative inline-block w-14 h-7 transition duration-200 ease-in-out">
                            <input type="hidden" name="settings[checkout_wallet_discount_enabled]" value="0">
                            <input type="checkbox" name="settings[checkout_wallet_discount_enabled]" value="1" 
                                id="wallet_enabled" class="hidden peer" {{ $walletDiscountEnabled ? 'checked' : '' }}>
                            <label for="wallet_enabled" 
                                class="block overflow-hidden h-7 rounded-full bg-gray-100 cursor-pointer peer-checked:bg-black transition-colors after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-all peer-checked:after:translate-x-7 shadow-inner">
                            </label>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="max-w-xs">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">Reward Percentage (%)</label>
                            <div class="relative group/input">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 pointer-events-none group-focus-within/input:text-black transition-colors">%</span>
                                <input type="number" min="0" max="100" name="settings[checkout_wallet_discount_percent]" 
                                    value="{{ old('settings.checkout_wallet_discount_percent', $walletDiscountPercent) }}" 
                                    data-discount-value="wallet"
                                    class="discount-input w-full rounded-2xl border-2 border-gray-100 px-5 py-4 text-lg font-bold focus:border-black focus:outline-none disabled:bg-gray-50/50 disabled:text-gray-300 disabled:border-gray-50 transition-all"
                                    placeholder="0"
                                    {{ !$walletDiscountEnabled ? 'disabled' : '' }}>
                            </div>
                            @error('settings.checkout_wallet_discount_percent')
                                <p class="text-xs text-red-500 mt-2 font-medium flex items-center gap-1.5">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="p-6 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="flex items-start gap-3">
                                <i data-lucide="gift" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                                <div class="space-y-2">
                                    <p class="text-xs leading-relaxed text-gray-500">
                                        <span class="text-black font-semibold">Promotional Benefit:</span> This is a special gift provided for used wallet coins. It is calculated on the total after coupons and online discounts. 
                                    </p>
                                    <div class="pt-2">
                                        <p class="text-[11px] font-bold text-black flex items-center gap-2" data-preview="wallet"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-8 z-10 pt-4">
                <button type="submit" class="w-full md:w-auto bg-black text-white px-10 py-5 rounded-2xl font-extrabold hover:bg-gray-800 transition-all shadow-[0_20px_40px_rgba(0,0,0,0.2)] flex items-center justify-center gap-3 active:scale-95 duration-200">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                    Update Discount Strategy
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function formatRupees(value) {
                return `₹${Number(value || 0).toFixed(2)}`;
            }

            function updateDiscountSection(section) {
                const valueInput = document.querySelector(`[data-discount-value="${section}"]`);
                const preview = document.querySelector(`[data-preview="${section}"]`);

                if (!valueInput || !preview) return;

                const value = parseFloat(valueInput.value || '0');
                const exampleOrder = 1000;
                const discount = (exampleOrder * value) / 100;

                preview.innerHTML = `
                    <span class="px-2 py-0.5 bg-black text-white rounded text-[10px] uppercase">Preview</span>
                    Order ${formatRupees(exampleOrder)} <i data-lucide="move-right" class="w-3 h-3 inline"></i> 
                    Discount <span class="text-emerald-600">${formatRupees(discount)}</span>
                `;
                
                // Re-init lucide for the dynamic content
                lucide.createIcons();
            }

            function bindDiscountPreview(section) {
                const valueInput = document.querySelector(`[data-discount-value="${section}"]`);
                const toggle = document.getElementById(`${section}_enabled`);

                if (!valueInput) return;

                if (toggle) {
                    toggle.addEventListener('change', () => {
                        valueInput.disabled = !toggle.checked;
                        if (!toggle.checked) {
                            valueInput.value = '0'; // Reset if disabled? Optional
                        }
                        updateDiscountSection(section);
                    });
                }

                valueInput.addEventListener('input', () => updateDiscountSection(section));
                updateDiscountSection(section);
            }

            document.addEventListener('DOMContentLoaded', () => {
                bindDiscountPreview('online');
                bindDiscountPreview('wallet');
            });
        </script>
    @endpush
@endsection
