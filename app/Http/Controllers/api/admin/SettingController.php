<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Setting;
use App\Http\Resources\Api\SettingResource;
use App\Http\Requests\Api\Admin\UpdateSettingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    /**
     * Display a listing of the settings.
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->groupBy('group');

        $formattedSettings = [];
        foreach ($settings as $group => $items) {
            $formattedSettings[$group] = SettingResource::collection($items);
        }

        return $this->success($formattedSettings, 'Settings retrieved successfully.');
    }

    /**
     * Display a specific setting by key.
     */
    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        return $this->success(new SettingResource($setting), 'Setting retrieved successfully.');
    }

    /**
     * Bulk update settings.
     */
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $settingsData = $request->input('settings');

        if (collect(array_keys($settingsData))->contains(fn ($key) => str_starts_with((string) $key, 'checkout_'))) {
            $settingsData = $this->normalizeCheckoutSettings($settingsData);
        }

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => (string) $value,
                    'group' => str_starts_with($key, 'checkout_') ? 'checkout' : 'general',
                ]
            );
        }

        // Return refreshed settings
        $settings = Setting::whereIn('key', array_keys($settingsData))->get();

        return $this->success(SettingResource::collection($settings), 'Settings updated successfully.');
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
            if (array_key_exists($key, $settings)) {
                $settings[$key] = (string) max(0, (int) round(((float) $settings[$key]) * 100));
            }
        }

        if (($settings['checkout_online_discount_type'] ?? 'percentage') === 'fixed' && array_key_exists('checkout_online_discount_value', $settings)) {
            $settings['checkout_online_discount_value'] = (string) max(0, (int) round(((float) $settings['checkout_online_discount_value']) * 100));
        }

        if (($settings['checkout_wallet_discount_type'] ?? 'percentage') === 'fixed' && array_key_exists('checkout_wallet_discount_value', $settings)) {
            $settings['checkout_wallet_discount_value'] = (string) max(0, (int) round(((float) $settings['checkout_wallet_discount_value']) * 100));
        }

        return $settings;
    }
}
