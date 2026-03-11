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

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
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

