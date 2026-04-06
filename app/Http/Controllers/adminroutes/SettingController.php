<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function shifts()
    {
        $shiftStart = Setting::get('shift_start_time', '09:00');
        $shiftDuration = Setting::get('shift_duration', '480');
        $withdrawDelayDays = Setting::get('withdraw_delay_days', '7');

        return view('settings.shifts', compact('shiftStart', 'shiftDuration', 'withdrawDelayDays'));
    }

    public function discounts()
    {
        $onlineDiscountEnabled = (bool) Setting::get('checkout_online_discount_enabled', '0');
        $onlineDiscountPercent = Setting::get('checkout_online_discount_percent', '0');
        $walletDiscountEnabled = (bool) Setting::get('checkout_wallet_discount_enabled', '0');
        $walletDiscountPercent = Setting::get('checkout_wallet_discount_percent', '0');

        return view('settings.discounts', compact(
            'onlineDiscountEnabled', 'onlineDiscountPercent',
            'walletDiscountEnabled', 'walletDiscountPercent'
        ));
    }

    public function updateDiscounts(Request $request)
    {
        // Checkboxes: if unchecked they are not submitted, so default to '0'
        Setting::set('checkout_online_discount_enabled', $request->has('settings.checkout_online_discount_enabled') ? '1' : '0');
        Setting::set('checkout_wallet_discount_enabled', $request->has('settings.checkout_wallet_discount_enabled') ? '1' : '0');

        if ($request->has('settings')) {
            foreach ($request->input('settings', []) as $key => $value) {
                Setting::set($key, (string) $value);
            }
        }

        return redirect()->route('settings.discounts')->with('success', 'Checkout discount settings updated successfully!');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'nullable|array',
            'shift_start_time' => 'nullable|string',
            'shift_duration' => 'nullable|integer|min:1|max:1440',
            'withdraw_delay_days' => 'nullable|integer|min:1|max:30',
        ]);

        $updated = false;

        if ($request->has('withdraw_delay_days')) {
            Setting::set('withdraw_delay_days', $request->withdraw_delay_days);
            $updated = true;
        }

        if ($request->has('shift_start_time')) {
            Setting::set('shift_start_time', $request->shift_start_time);
            $updated = true;
        }

        if ($request->has('shift_duration')) {
            Setting::set('shift_duration', $request->shift_duration);
            $updated = true;
        }

        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                Setting::set($key, (string) $value);
            }
            $updated = true;
        }

        if ($updated) {
            return redirect()->back()->with('success', 'Settings updated successfully!');
        }

        return redirect()->back();
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

    public function updateRewards(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.status' => 'required|in:0,1',
            'rules.*.coins' => 'required|integer|min:0|max:99999',
        ]);

        foreach ($request->rules as $id => $data) {
            $rule = \App\Models\RewardRule::findOrFail($id);
            $rule->update([
                'status' => $data['status'],
                'coins' => $data['coins'],
            ]);
        }

        return redirect()->route('referrals.index')->with('success', 'Reward rules updated successfully!');
    }
}
