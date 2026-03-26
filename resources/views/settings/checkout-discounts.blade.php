@extends('layouts.app')

@section('title', 'Pricing & Discounts · Bellavella Admin')

@section('content')
    @php
        $toRupees = fn ($paise) => number_format(((int) $paise) / 100, 2, '.', '');
        $checkoutDiscountsEnabled = optional($settings->where('key', 'checkout_discounts_enabled')->first())->value ?? '0';
        $onlineEnabled = optional($settings->where('key', 'checkout_online_discount_enabled')->first())->value ?? '0';
        $onlineType = optional($settings->where('key', 'checkout_online_discount_type')->first())->value ?? 'percentage';
        $onlineStoredValue = optional($settings->where('key', 'checkout_online_discount_value')->first())->value ?? '0';
        $onlineValue = $onlineType === 'fixed' ? $toRupees($onlineStoredValue) : $onlineStoredValue;
        $onlineMinOrder = $toRupees(optional($settings->where('key', 'checkout_online_discount_min_order_paise')->first())->value ?? '0');
        $onlineMaxCap = $toRupees(optional($settings->where('key', 'checkout_online_discount_max_cap_paise')->first())->value ?? '0');
        $walletEnabled = optional($settings->where('key', 'checkout_wallet_discount_enabled')->first())->value ?? '0';
        $walletType = optional($settings->where('key', 'checkout_wallet_discount_type')->first())->value ?? 'percentage';
        $walletStoredValue = optional($settings->where('key', 'checkout_wallet_discount_value')->first())->value ?? '0';
        $walletValue = $walletType === 'fixed' ? $toRupees($walletStoredValue) : $walletStoredValue;
        $walletMinOrder = $toRupees(optional($settings->where('key', 'checkout_wallet_discount_min_order_paise')->first())->value ?? '0');
        $walletMaxCap = $toRupees(optional($settings->where('key', 'checkout_wallet_discount_max_cap_paise')->first())->value ?? '0');
        $allowCombined = optional($settings->where('key', 'checkout_allow_combined_discount')->first())->value ?? '0';
        $totalDiscountMaxCap = $toRupees(optional($settings->where('key', 'checkout_total_discount_max_cap_paise')->first())->value ?? '0');
    @endphp

    <div class="max-w-6xl mx-auto mt-4">
        <div class="bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400">Pricing & Discounts</p>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight mt-2">Checkout Discounts</h1>
                    <p class="text-sm text-gray-500 mt-2 max-w-2xl">Configure global checkout discounts that apply across all orders. Theme and branding stay in General Settings.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="checkout-discount-settings-form" action="{{ route('settings.update') }}" method="POST" class="mt-8 space-y-8">
                @csrf

                <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-bold text-gray-900">Checkout Discounts</h2>
                                <input type="hidden" name="settings[checkout_discounts_enabled]" value="0">
                                <label class="inline-flex items-center gap-3 rounded-full bg-white px-4 py-2 ring-1 ring-gray-200">
                                    <input type="checkbox" name="settings[checkout_discounts_enabled]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $checkoutDiscountsEnabled === '1' ? 'checked' : '' }}>
                                    <span class="text-sm font-semibold text-gray-800">Enable</span>
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Turn this off to disable all global checkout discounts without touching coupon and offer rules.</p>
                        </div>

                        <div class="w-full xl:w-72 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4">
                            <label class="block text-sm font-bold text-amber-900 mb-2">Maximum Total Discount Cap (₹)</label>
                            <input type="number" min="0" step="0.01" name="settings[checkout_total_discount_max_cap_paise]" value="{{ $totalDiscountMaxCap }}" class="w-full rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:outline-none">
                            <p class="mt-2 text-xs text-amber-700">Prevents excessive discounting across the full checkout. Use 0 for no cap.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-gray-100 p-6" data-discount-card="online">
                        <div class="mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Online Payment Discount</h3>
                            <p class="text-sm text-gray-500 mt-1">Applies only when checkout payment method is <code>online</code>. Discount is calculated on the total order amount.</p>
                        </div>

                        <input type="hidden" name="settings[checkout_online_discount_enabled]" value="0">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700 mb-5">
                            <input type="checkbox" name="settings[checkout_online_discount_enabled]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $onlineEnabled === '1' ? 'checked' : '' }}>
                            Enable online payment discount
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
                                <select name="settings[checkout_online_discount_type]" data-discount-type="online" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                    <option value="percentage" {{ $onlineType === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ $onlineType === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Value</label>
                                <input type="number" min="0" step="0.01" data-discount-value="online" name="settings[checkout_online_discount_value]" value="{{ $onlineValue }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400" data-discount-hint="online">Percentage: % of order. Fixed Amount: ₹ amount discount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_online_discount_min_order_paise]" value="{{ $onlineMinOrder }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Applies only if subtotal meets this amount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Discount Cap (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_online_discount_max_cap_paise]" value="{{ $onlineMaxCap }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Use 0 for no cap.</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Preview</p>
                            <p data-preview="online">On ₹1,000.00 order: You give ₹0.00 discount. Customer pays ₹1,000.00.</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 p-6" data-discount-card="wallet">
                        <div class="mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Wallet Usage Discount</h3>
                            <p class="text-sm text-gray-500 mt-1">Applies only when wallet coins are actually used in this order. Discount is calculated on the total order amount, not on wallet amount.</p>
                        </div>

                        <input type="hidden" name="settings[checkout_wallet_discount_enabled]" value="0">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700 mb-5">
                            <input type="checkbox" name="settings[checkout_wallet_discount_enabled]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $walletEnabled === '1' ? 'checked' : '' }}>
                            Enable wallet usage discount
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
                                <select name="settings[checkout_wallet_discount_type]" data-discount-type="wallet" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                    <option value="percentage" {{ $walletType === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ $walletType === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Value</label>
                                <input type="number" min="0" step="0.01" data-discount-value="wallet" name="settings[checkout_wallet_discount_value]" value="{{ $walletValue }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400" data-discount-hint="wallet">Percentage: % of order. Fixed Amount: ₹ amount discount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_wallet_discount_min_order_paise]" value="{{ $walletMinOrder }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Applies only if subtotal meets this amount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Discount Cap (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_wallet_discount_max_cap_paise]" value="{{ $walletMaxCap }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Use 0 for no cap.</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Preview</p>
                            <p data-preview="wallet">On ₹1,000.00 order: You give ₹0.00 discount. Customer pays ₹1,000.00.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-6">
                    <div class="flex items-center gap-3">
                        <i data-lucide="git-merge" class="w-5 h-5 text-sky-700"></i>
                        <h3 class="text-lg font-bold text-gray-900">Combination Logic</h3>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 mb-4">Choose whether online and wallet discounts may stack in the same checkout.</p>
                    <input type="hidden" name="settings[checkout_allow_combined_discount]" value="0">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="settings[checkout_allow_combined_discount]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $allowCombined === '1' ? 'checked' : '' }}>
                        Allow both discounts together
                    </label>
                    <p class="mt-2 text-xs text-gray-500">If enabled, both discounts apply and are added together. If disabled, only the higher of the two discounts will apply. Each rule still respects its own cap, and the global total cap applies last.</p>
                </div>

                <div class="sticky bottom-4 z-10 flex items-center justify-end">
                    <button type="submit" class="bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
                        Save Checkout Discount Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatRupees(value) {
                return `₹${Number(value || 0).toFixed(2)}`;
            }

            function updateDiscountSection(section) {
                const typeInput = document.querySelector(`[data-discount-type="${section}"]`);
                const valueInput = document.querySelector(`[data-discount-value="${section}"]`);
                const hint = document.querySelector(`[data-discount-hint="${section}"]`);
                const preview = document.querySelector(`[data-preview="${section}"]`);

                if (!typeInput || !valueInput || !hint || !preview) {
                    return;
                }

                const type = typeInput.value;
                const value = parseFloat(valueInput.value || '0');
                const exampleOrder = 1000;
                let discount = 0;

                if (type === 'fixed') {
                    hint.textContent = 'Fixed Amount: flat rupee discount.';
                    discount = value;
                } else {
                    hint.textContent = 'Percentage: % of order.';
                    discount = (exampleOrder * value) / 100;
                }

                const payable = Math.max(0, exampleOrder - discount);
                preview.textContent = `On ${formatRupees(exampleOrder)} order: You give ${formatRupees(discount)} discount. Customer pays ${formatRupees(payable)}.`;
            }

            function bindDiscountPreview(section) {
                const typeInput = document.querySelector(`[data-discount-type="${section}"]`);
                const valueInput = document.querySelector(`[data-discount-value="${section}"]`);

                if (!typeInput || !valueInput) {
                    return;
                }

                typeInput.addEventListener('change', () => updateDiscountSection(section));
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
