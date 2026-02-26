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

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Return refreshed settings
        $settings = Setting::whereIn('key', array_keys($settingsData))->get();

        return $this->success(SettingResource::collection($settings), 'Settings updated successfully.');
    }
}
