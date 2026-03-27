@extends('layouts.app')

@section('title', 'App Theme Settings · Bellavella Admin')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row gap-8 items-start mt-4">

            <!-- Left: Form -->
            <div class="flex-1 bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">App Theme Configuration</h2>
                    <p class="text-sm text-gray-400 mt-1">Customize the look and feel of your client-side application. Changes are reflected live in the APK.</p>
                </div>

                <!-- Toast notification -->
                <div id="toast" class="hidden mb-6 p-4 rounded-xl text-sm font-medium flex items-center gap-2 transition-all"></div>

                @php
                    $primary    = optional($settings->where('key', 'primary_color')->first())->value    ?? '#FF4D7D';
                    $secondary  = optional($settings->where('key', 'secondary_color')->first())->value  ?? '#6B7280';
                    $background = optional($settings->where('key', 'background_color')->first())->value ?? '#F6F7F9';
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

                <div class="grid grid-cols-1 gap-6">

                    <!-- Primary Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                            Primary Color
                        </label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_color" value="{{ $primary }}" oninput="updatePreview()">
                            <div class="color-info" id="primary_hex">{{ $primary }}</div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for buttons, icons, and main UI highlights.</p>
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                            Secondary Color
                        </label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_color" value="{{ $secondary }}" oninput="updatePreview()">
                            <div class="color-info" id="secondary_hex">{{ $secondary }}</div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for accents, secondary buttons, and text highlights.</p>
                    </div>

                    <!-- Background Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                            App Background
                        </label>
                        <div class="color-input-wrapper">
                            <input type="color" id="background_color" value="{{ $background }}" oninput="updatePreview()">
                            <div class="color-info" id="background_hex">{{ $background }}</div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5 ml-1">The primary background color for pages and containers.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center gap-3 mt-6">
                    <button onclick="saveTheme()" id="saveBtn"
                        class="flex-1 bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/5 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span id="saveBtnText">Save Configuration</span>
                    </button>
                    <button onclick="resetTheme()" id="resetBtn"
                        class="px-6 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition-all flex items-center gap-2">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Right: Preview -->
            <div class="w-full md:w-auto flex flex-col items-center flex-shrink-0 md:sticky md:top-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">APK Live Preview</p>
                <div class="phone-mockup">
                    <div class="phone-screen" id="preview-screen">
                        <div class="phone-header flex items-center justify-between px-4 py-3">
                            <i data-lucide="menu" class="w-5 h-5 text-gray-800" id="preview-icon-menu"></i>
                            <span class="text-sm font-bold text-gray-800" id="preview-app-name">Bellavella</span>
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-gray-800" id="preview-icon-cart"></i>
                        </div>
                        <div class="phone-content space-y-4 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900" id="preview-title">Exclusive Offers</h3>
                                <span class="text-xs font-semibold" id="preview-see-all">See All</span>
                            </div>

                            <div class="phone-card bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i data-lucide="sparkles" class="w-6 h-6" id="preview-icon-svc"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-3 w-2/3 bg-gray-100 rounded-full mb-2"></div>
                                        <div class="h-2 w-1/3 bg-gray-50 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="phone-card bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i data-lucide="heart" class="w-6 h-6" id="preview-icon-heart"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-3 w-1/2 bg-gray-100 rounded-full mb-2"></div>
                                        <div class="h-2 w-1/4 bg-gray-50 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="phone-btn text-white shadow-md p-4 rounded-2xl text-center font-bold"
                                id="preview-main-btn">
                                Book Appointment
                            </div>

                            <div class="grid grid-cols-4 gap-2 mt-8">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100"
                                        id="preview-nav-1">
                                        <i data-lucide="home" class="w-4 h-4 text-white" id="preview-nav-icon-1"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50"
                                        id="preview-nav-2">
                                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Notch -->
                    <div
                        class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-6 bg-black rounded-b-xl flex items-end justify-center pb-1">
                        <div class="w-8 h-1 bg-white/20 rounded-full"></div>
                    </div>
                </div>
                <p class="mt-4 text-[10px] text-gray-400 font-medium">Visual representation only</p>
            </div>

        </div>

        <!-- Withdrawal Settings -->
        <div class="mt-8 bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Withdrawal Settings</h2>
                <p class="text-sm text-gray-400 mt-1">Configure the cooldown period for professional earnings. Money is locked for X days after job completion.</p>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="max-w-md">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                        Withdraw Delay (Days)
                    </label>
                    <input type="number" name="withdraw_delay_days" min="1" max="7" 
                        value="{{ \App\Models\Setting::get('withdraw_delay_days', 3) }}"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none"
                        required>
                    <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Must be between 1 and 7 days. Higher value reduces fraud risk.</p>
                </div>

                <button type="submit" 
                    class="bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/5 flex items-center justify-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    Update Withdrawal Delay
                </button>
            </form>
        </div>

        @if(false)
        <div class="mt-8 bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Checkout Discount Settings</h2>
                    <p class="text-sm text-gray-400 mt-1">Configure global checkout discounts that apply across all orders. Coupon and offer discounts continue to work separately.</p>
                </div>
                <button type="submit" form="checkout-discount-settings-form" class="bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/5 w-full lg:w-auto">
                    Save Checkout Discount Settings
                </button>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="checkout-discount-settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="rounded-2xl border border-gray-100 p-6 bg-gray-50/50">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Master Control</h3>
                            <p class="text-sm text-gray-500 mt-1">Disable this to turn off global checkout discounts completely without touching coupon and offer rules.</p>
                        </div>
                        <div class="flex items-center gap-6 flex-wrap">
                            <div>
                                <input type="hidden" name="settings[checkout_discounts_enabled]" value="0">
                                <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="settings[checkout_discounts_enabled]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $checkoutDiscountsEnabled === '1' ? 'checked' : '' }}>
                                    Enable Checkout Discounts
                                </label>
                            </div>
                            <div class="w-full lg:w-56">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Total Discount Cap (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_total_discount_max_cap_paise]" value="{{ $totalDiscountMaxCap }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Use 0 for no global total cap.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-gray-100 p-6" data-discount-card="online">
                        <div class="mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Online Payment Discount</h3>
                            <p class="text-sm text-gray-400 mt-1">Applies only when checkout payment method is <code>online</code>. Discount is calculated on total order amount.</p>
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
                                <p class="mt-1 text-xs text-gray-400" data-discount-hint="online">Percentage mode: % of order. Fixed mode: ₹ amount discount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_online_discount_min_order_paise]" value="{{ $onlineMinOrder }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Discount applies only if subtotal meets this amount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Discount Cap (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_online_discount_max_cap_paise]" value="{{ $onlineMaxCap }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Use 0 for no cap.</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Preview</p>
                            <p data-preview="online">Example: Order ₹1,000.00 → Discount ₹0.00</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 p-6" data-discount-card="wallet">
                        <div class="mb-5">
                            <h3 class="text-lg font-bold text-gray-900">Wallet Usage Discount</h3>
                            <p class="text-sm text-gray-400 mt-1">Applies when customer uses wallet coins during checkout. Discount is calculated on total order amount, not on wallet amount.</p>
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
                                <p class="mt-1 text-xs text-gray-400" data-discount-hint="wallet">Percentage mode: % of order. Fixed mode: ₹ amount discount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_wallet_discount_min_order_paise]" value="{{ $walletMinOrder }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Discount applies only if subtotal meets this amount.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Discount Cap (₹)</label>
                                <input type="number" min="0" step="0.01" name="settings[checkout_wallet_discount_max_cap_paise]" value="{{ $walletMaxCap }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-black focus:outline-none">
                                <p class="mt-1 text-xs text-gray-400">Use 0 for no cap.</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Preview</p>
                            <p data-preview="wallet">Example: Order ₹1,000.00 → Discount ₹0.00</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900">Combination</h3>
                    <p class="text-sm text-gray-400 mt-1 mb-4">Choose whether online and wallet discounts may stack in the same checkout.</p>
                    <input type="hidden" name="settings[checkout_allow_combined_discount]" value="0">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="settings[checkout_allow_combined_discount]" value="1" class="rounded border-gray-300 text-black focus:ring-black" {{ (string) $allowCombined === '1' ? 'checked' : '' }}>
                        Allow both discounts together
                    </label>
                    <p class="mt-2 text-xs text-gray-400">If enabled, both discounts apply and are added together. If disabled, only the higher of the two discounts will apply. Each rule still respects its own cap, and the global total cap applies last.</p>
                </div>

                <div class="sticky bottom-4 z-10 flex items-center justify-end">
                    <button type="submit" class="bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
                        Save Checkout Discount Settings
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    @push('styles')
        <style>
            .color-input-wrapper {
                position: relative;
                width: 100%;
                height: 48px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                overflow: hidden;
                background: #fff;
                transition: all 0.2s;
            }

            .color-input-wrapper:hover { border-color: #000; }

            .color-input-wrapper input[type="color"] {
                position: absolute;
                top: -10px; left: -10px;
                width: 150%; height: 150%;
                cursor: pointer; padding: 0; border: none;
            }

            .color-info {
                position: absolute;
                right: 12px; top: 50%;
                transform: translateY(-50%);
                pointer-events: none;
                font-size: 13px; font-weight: 500; color: #374151;
                background: rgba(255,255,255,0.9);
                padding: 2px 8px; border-radius: 6px;
                border: 1px solid #f3f4f6;
            }

            .phone-mockup {
                width: 280px; height: 560px;
                background: #000; border-radius: 36px;
                padding: 10px;
                box-shadow: 0 20px 50px rgba(0,0,0,0.1);
                position: relative;
            }

            .phone-screen {
                width: 100%; height: 100%;
                background: #fff; border-radius: 28px;
                overflow: hidden; display: flex;
                flex-direction: column; position: relative;
            }

            #saveBtn:disabled { opacity: 0.6; cursor: not-allowed; }
        </style>
    @endpush

    @push('scripts')
        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            // ── Live Preview ──────────────────────────────────────────────────
            function updatePreview() {
                const primary    = document.getElementById('primary_color').value;
                const secondary  = document.getElementById('secondary_color').value;
                const background = document.getElementById('background_color').value;

                document.getElementById('primary_hex').textContent    = primary;
                document.getElementById('secondary_hex').textContent  = secondary;
                document.getElementById('background_hex').textContent = background;

                document.getElementById('preview-screen').style.backgroundColor   = background;
                document.getElementById('preview-main-btn').style.backgroundColor = primary;
                document.getElementById('preview-nav-1').style.backgroundColor    = primary;
                document.getElementById('preview-icon-menu').style.color          = primary;
                document.getElementById('preview-icon-cart').style.color          = primary;
                document.getElementById('preview-icon-svc').style.color           = primary;
                document.getElementById('preview-see-all').style.color            = secondary;
                document.getElementById('preview-icon-heart').style.color         = secondary;
            }

            // ── Toast helper ──────────────────────────────────────────────────
            function showToast(message, isSuccess = true) {
                const toast = document.getElementById('toast');
                toast.className = `mb-6 p-4 rounded-xl text-sm font-medium flex items-center gap-2 transition-all ${
                    isSuccess ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'
                }`;
                toast.innerHTML = `<span>${isSuccess ? '✅' : '❌'}</span> ${message}`;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3500);
            }

            // ── AJAX Save ─────────────────────────────────────────────────────
            async function saveTheme() {
                const btn     = document.getElementById('saveBtn');
                const btnText = document.getElementById('saveBtnText');
                btn.disabled  = true;
                btnText.textContent = 'Saving...';

                try {
                    const res = await fetch('{{ route("settings.theme.save") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({
                            primary_color:    document.getElementById('primary_color').value,
                            secondary_color:  document.getElementById('secondary_color').value,
                            background_color: document.getElementById('background_color').value,
                        }),
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        showToast('Theme saved! Flutter app will pick up changes on next launch.');
                    } else {
                        showToast('Failed to save theme.', false);
                    }
                } catch (e) {
                    showToast('Network error. Please try again.', false);
                } finally {
                    btn.disabled        = false;
                    btnText.textContent = 'Save Configuration';
                }
            }

            // ── AJAX Reset ────────────────────────────────────────────────────
            async function resetTheme() {
                const btn = document.getElementById('resetBtn');
                btn.disabled = true;

                try {
                    const res  = await fetch('{{ route("settings.theme.reset") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF },
                    });
                    const data = await res.json();

                    // Restore inputs
                    document.getElementById('primary_color').value    = data.primary_color;
                    document.getElementById('secondary_color').value  = data.secondary_color;
                    document.getElementById('background_color').value = data.background_color;

                    // Reflect in preview
                    updatePreview();
                    showToast('Theme reset to Bellavella defaults!');
                } catch (e) {
                    showToast('Network error. Please try again.', false);
                } finally {
                    btn.disabled = false;
                }
            }

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
                    hint.textContent = 'Fixed Amount (₹): flat rupee discount.';
                    discount = value;
                } else {
                    hint.textContent = 'Percentage: % of order.';
                    discount = (exampleOrder * value) / 100;
                }

                preview.textContent = `Example: Order ${formatRupees(exampleOrder)} → Discount ${formatRupees(discount)}`;
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
                updatePreview();
                bindDiscountPreview('online');
                bindDiscountPreview('wallet');
            });
        </script>
    @endpush
@endsection
