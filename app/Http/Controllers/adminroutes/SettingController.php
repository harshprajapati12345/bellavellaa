<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const CHECKOUT_DISCOUNT_DEFAULTS = [
        'checkout_discounts_enabled' => '0',
        'checkout_online_discount_enabled' => '0',
        'checkout_online_discount_type' => 'percentage',
        'checkout_online_discount_value' => '0',
        'checkout_online_discount_min_order_paise' => '0',
        'checkout_online_discount_max_cap_paise' => '0',
        'checkout_wallet_discount_enabled' => '0',
        'checkout_wallet_discount_type' => 'percentage',
        'checkout_wallet_discount_value' => '0',
        'checkout_wallet_discount_min_order_paise' => '0',
        'checkout_wallet_discount_max_cap_paise' => '0',
        'checkout_allow_combined_discount' => '0',
        'checkout_total_discount_max_cap_paise' => '0',
    ];

    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function checkoutDiscounts()
    {
        $settings = Setting::all();
        return view('settings.checkout-discounts', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.checkout_discounts_enabled' => 'nullable|boolean',
            'settings.checkout_online_discount_enabled' => 'nullable|boolean',
            'settings.checkout_online_discount_type' => 'nullable|in:percentage,fixed',
            'settings.checkout_online_discount_value' => 'nullable|numeric|min:0',
            'settings.checkout_online_discount_min_order_paise' => 'nullable|numeric|min:0',
            'settings.checkout_online_discount_max_cap_paise' => 'nullable|numeric|min:0',
            'settings.checkout_wallet_discount_enabled' => 'nullable|boolean',
            'settings.checkout_wallet_discount_type' => 'nullable|in:percentage,fixed',
            'settings.checkout_wallet_discount_value' => 'nullable|numeric|min:0',
            'settings.checkout_wallet_discount_min_order_paise' => 'nullable|numeric|min:0',
            'settings.checkout_wallet_discount_max_cap_paise' => 'nullable|numeric|min:0',
            'settings.checkout_allow_combined_discount' => 'nullable|boolean',
            'settings.checkout_total_discount_max_cap_paise' => 'nullable|numeric|min:0',
        ]);

        $settings = $data['settings'];

        $hasCheckoutSettings = collect(array_keys($settings))
            ->contains(fn ($key) => str_starts_with((string) $key, 'checkout_'));

        if ($hasCheckoutSettings) {
            foreach (self::CHECKOUT_DISCOUNT_DEFAULTS as $key => $default) {
                if (!array_key_exists($key, $settings)) {
                    $settings[$key] = $default;
                }
            }

            $settings = $this->normalizeCheckoutSettings($settings);
        }

        foreach ($settings as $key => $value) {
            $group = str_starts_with($key, 'checkout_') ? 'checkout' : 'general';
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value, 'group' => $group]);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function store(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Settings updated successfully.');
    }

    // ─── AJAX endpoints ───────────────────────────────────────────────

    public function saveTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color'    => 'required|string|max:20',
            'secondary_color'  => 'required|string|max:20',
            'background_color' => 'required|string|max:20',
        ]);

        Setting::set('primary_color',    $validated['primary_color']);
        Setting::set('secondary_color',  $validated['secondary_color']);
        Setting::set('background_color', $validated['background_color']);

        return response()->json(['status' => 'success', 'message' => 'Theme saved successfully!']);
    }

    public function resetTheme()
    {
        Setting::set('primary_color',    '#FF4D7D');
        Setting::set('secondary_color',  '#6B7280');
        Setting::set('background_color', '#F6F7F9');

        return response()->json([
            'status'           => 'success',
            'primary_color'    => '#FF4D7D',
            'secondary_color'  => '#6B7280',
            'background_color' => '#F6F7F9',
        ]);
    }

    private function normalizeCheckoutSettings(array $settings): array
    {
        $amountKeys = [
            'checkout_online_discount_min_order_paise',
            'checkout_online_discount_max_cap_paise',
            'checkout_wallet_discount_min_order_paise',
            'checkout_wallet_discount_max_cap_paise',
            'checkout_total_discount_max_cap_paise',
        ];

        foreach ($amountKeys as $key) {
            $settings[$key] = $this->rupeesToPaise($settings[$key] ?? 0);
        }

        if (($settings['checkout_online_discount_type'] ?? 'percentage') === 'fixed') {
            $settings['checkout_online_discount_value'] = $this->rupeesToPaise($settings['checkout_online_discount_value'] ?? 0);
        } else {
            $settings['checkout_online_discount_value'] = $this->normalizeDecimal($settings['checkout_online_discount_value'] ?? 0);
        }

        if (($settings['checkout_wallet_discount_type'] ?? 'percentage') === 'fixed') {
            $settings['checkout_wallet_discount_value'] = $this->rupeesToPaise($settings['checkout_wallet_discount_value'] ?? 0);
        } else {
            $settings['checkout_wallet_discount_value'] = $this->normalizeDecimal($settings['checkout_wallet_discount_value'] ?? 0);
        }

        return $settings;
    }

    private function rupeesToPaise($value): string
    {
        return (string) max(0, (int) round(((float) $value) * 100));
    }

    private function normalizeDecimal($value): string
    {
        $normalized = max(0, (float) $value);

        return fmod($normalized, 1.0) === 0.0
            ? (string) (int) $normalized
            : (string) $normalized;
    }
}
