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
     * POST /api/professional/notifications/{id}/read
     */
    public function read(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        ProfessionalNotification::where('id', $id)
            ->where('professional_id', $professional->id)
            ->update(['read_at' => now()]);

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * POST /api/professional/notifications/read-all
     */
    public function readAll(Request $request): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        ProfessionalNotification::where('professional_id', $professional->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->success(null, 'All notifications marked as read.');
    }

    /**
     * DELETE /api/professional/notifications/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $professional = $request->user('professional-api');
        
        ProfessionalNotification::where('id', $id)
            ->where('professional_id', $professional->id)
            ->delete();

        return $this->success(null, 'Notification deleted.');
    }
}
