<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class ThemeController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'primary_color'    => Setting::get('primary_color',    '#FF4D7D'),
            'secondary_color'  => Setting::get('secondary_color',  '#6B7280'),
            'background_color' => Setting::get('background_color', '#F6F7F9'),
        ]);
    }
}
