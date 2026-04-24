<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends BaseController
{
    /**
     * GET /api/v1/client/settings
     */
    public function index(): JsonResponse
    {
        return $this->success([
            'tip_enabled' => Setting::getBool('tip_enabled', false),
            'tip_amounts' => Setting::get('tip_amounts', '50,75,100'),
        ], 'Settings retrieved successfully.');
    }
}
