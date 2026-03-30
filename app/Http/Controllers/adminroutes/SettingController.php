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

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required_without_all:shift_start_time,shift_duration|array',
            'shift_start_time' => 'nullable|string',
            'shift_duration' => 'nullable|integer|min:1|max:1440',
            'withdraw_delay_days' => 'nullable|integer|min:1|max:7',
        ]);

        if ($request->has('withdraw_delay_days')) {
            Setting::set('withdraw_delay_days', $request->withdraw_delay_days);
            if (!$request->has(['shift_start_time', 'shift_duration', 'settings'])) {
                return redirect()->back()->with('success', 'Withdrawal delay updated successfully!');
            }
        }

        if ($request->has(['shift_start_time', 'shift_duration'])) {
            Setting::set('shift_start_time', $request->shift_start_time);
            Setting::set('shift_duration', $request->shift_duration);

            return redirect()->back()->with('success', 'Shift updated successfully!');
        }

        $settings = $data['settings'];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value, 'group' => 'general']);
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
}
