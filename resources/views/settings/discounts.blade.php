@extends('layouts.app')
@section('title', 'Checkout Discounts')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Checkout Discounts</h1>
        <p class="text-sm text-gray-400 mt-1">Set percentage discounts applied at checkout for online payments and wallet usage.</p>
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

    <form action="{{ route('settings.discounts.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Online Payment Discount --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Online Payment Discount</h2>
                        <p class="text-xs text-gray-400">Applied when customers pay via Razorpay / UPI</p>
                    </div>
                </div>
                <div class="relative inline-block w-12 h-6">
                    <input type="hidden" name="settings[checkout_online_discount_enabled]" value="0">
                    <input type="checkbox" name="settings[checkout_online_discount_enabled]" value="1"
                        id="online_enabled" class="hidden peer" {{ $onlineDiscountEnabled ? 'checked' : '' }}>
                    <label for="online_enabled"
                        class="block h-6 rounded-full bg-gray-200 cursor-pointer peer-checked:bg-black transition-colors relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner">
                    </label>
                </div>
            </div>

            <div class="max-w-xs">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 pointer-events-none">%</span>
                    <input type="number" min="0" max="100" step="0.01"
                        name="settings[checkout_online_discount_percent]"
                        value="{{ old('settings.checkout_online_discount_percent', $onlineDiscountPercent) }}"
                        data-discount-value="online"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-10 text-sm focus:border-black focus:outline-none transition-all {{ !$onlineDiscountEnabled ? 'bg-gray-50 text-gray-400' : '' }}"
                        placeholder="0"
                        {{ !$onlineDiscountEnabled ? 'disabled' : '' }}>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Example: Enter <strong>5</strong> to give 5% off on online payments.</p>
            </div>

            <div class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-gray-400 shrink-0"></i>
                <p data-preview="online">Example: Order ₹1,000.00 → Discount ₹0.00</p>
            </div>
        </div>

        {{-- Wallet Discount --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5 text-violet-600"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Wallet Usage Reward</h2>
                        <p class="text-xs text-gray-400">Applied when customers pay using Bellavella wallet points</p>
                    </div>
                </div>
                <div class="relative inline-block w-12 h-6">
                    <input type="hidden" name="settings[checkout_wallet_discount_enabled]" value="0">
                    <input type="checkbox" name="settings[checkout_wallet_discount_enabled]" value="1"
                        id="wallet_enabled" class="hidden peer" {{ $walletDiscountEnabled ? 'checked' : '' }}>
                    <label for="wallet_enabled"
                        class="block h-6 rounded-full bg-gray-200 cursor-pointer peer-checked:bg-black transition-colors relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner">
                    </label>
                </div>
            </div>

            <div class="max-w-xs">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 pointer-events-none">%</span>
                    <input type="number" min="0" max="100" step="0.01"
                        name="settings[checkout_wallet_discount_percent]"
                        value="{{ old('settings.checkout_wallet_discount_percent', $walletDiscountPercent) }}"
                        data-discount-value="wallet"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-10 text-sm focus:border-black focus:outline-none transition-all {{ !$walletDiscountEnabled ? 'bg-gray-50 text-gray-400' : '' }}"
                        placeholder="0"
                        {{ !$walletDiscountEnabled ? 'disabled' : '' }}>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Example: Enter <strong>3</strong> to give 3% off when wallet is used.</p>
            </div>

            <div class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600 flex items-center gap-2">
                <i data-lucide="gift" class="w-4 h-4 text-gray-400 shrink-0"></i>
                <p data-preview="wallet">Example: Order ₹1,000.00 → Discount ₹0.00</p>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                class="bg-black text-white px-8 py-3 rounded-xl font-semibold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-md">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Save Discount Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function formatRupees(v) {
        return '₹' + Number(v || 0).toFixed(2);
    }

    function updatePreview(section) {
        const valueInput = document.querySelector(`[data-discount-value="${section}"]`);
        const preview    = document.querySelector(`[data-preview="${section}"]`);
        if (!valueInput || !preview) return;
        const pct = parseFloat(valueInput.value || '0');
        const exampleOrder = 1000;
        const discount = (exampleOrder * pct) / 100;
        preview.textContent = `Example: Order ${formatRupees(exampleOrder)} → Discount ${formatRupees(discount)}`;
    }

    function bindSection(section) {
        const toggle = document.getElementById(`${section}_enabled`);
        const input  = document.querySelector(`[data-discount-value="${section}"]`);
        if (!input) return;
        if (toggle) {
            toggle.addEventListener('change', () => {
                input.disabled = !toggle.checked;
                input.classList.toggle('bg-gray-50', !toggle.checked);
                input.classList.toggle('text-gray-400', !toggle.checked);
                if (!toggle.checked) input.value = '0';
                updatePreview(section);
            });
        }
        input.addEventListener('input', () => updatePreview(section));
        updatePreview(section);
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindSection('online');
        bindSection('wallet');
    });
</script>
@endpush
@endsection
