<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\CustomerAppFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppFeedbackController extends BaseController
{
    /**
     * Store a newly created app feedback in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
            'device_info' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
        ]);

        $feedback = CustomerAppFeedback::create([
            'customer_id' => Auth::guard('api')->id(),
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'] ?? null,
            'device_info' => $validated['device_info'] ?? null,
            'app_version' => $validated['app_version'] ?? null,
        ]);

        return $this->success($feedback, 'Feedback submitted successfully.');
    }
}
