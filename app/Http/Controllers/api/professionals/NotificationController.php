<?php

namespace App\Http\Controllers\Api\Professionals;

use App\Models\ProfessionalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    /**
     * GET /api/professionals/notifications
     * List recent notifications
     */
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');

        $notifications = ProfessionalNotification::where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->success($notifications, 'Notifications retrieved.');
    }

    /**
     * POST /api/professionals/notifications/read
     * Mark notifications as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        $validated = $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:professional_notifications,id',
        ]);

        ProfessionalNotification::whereIn('id', $validated['notification_ids'])
            ->where('professional_id', $professional->id)
            ->update(['read_at' => now()]);

        return $this->success(null, 'Notifications marked as read.');
    }
}
