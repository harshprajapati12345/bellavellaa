<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\CustomerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    /**
     * GET /api/client/notifications
     */
    public function index(): JsonResponse
    {
        $notifications = $this->guard()->user()->notifications()
            ->latest()
            ->paginate(20);

        return $this->success($notifications, 'Notifications retrieved successfully.');
    }

    /**
     * POST /api/client/notifications/{id}/read
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = CustomerNotification::where('customer_id', $this->guard()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * POST /api/client/notifications/read-all
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->guard()->user()->notifications()->unread()->update(['read_at' => now()]);

        return $this->success(null, 'All notifications marked as read.');
    }

    /**
     * DELETE /api/client/notifications/{id}
     */
    public function destroy($id): JsonResponse
    {
        $notification = CustomerNotification::where('customer_id', $this->guard()->id())
            ->findOrFail($id);

        $notification->delete();

        return $this->success(null, 'Notification deleted.');
    }
}
