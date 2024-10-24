<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Js;

class NotificationController extends Controller
{
    use HttpResponses;

    public function getAllNotificationsByCustomerId(Request $request): JsonResponse
    {
        $user = auth()->user();
        try {
            $notifications = Notification::where('user_id', $user->user_id)->get();
            if ($notifications->isEmpty()) {
                return $this->fail("Not found notifications with user id " . $user->user_id, 404);
            }
            return $this->success($notifications);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(), 500);
        }
    }

    public function updateReadNotification(HttpRequest $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            "notification_id" => ['required', 'numeric'],
        ]);
        try {
            $notification = Notification::where('notification_id', $validated['notification_id'])->first();
            if ($notification['user_id'] === $user['user_id']) {
                $notification->is_read = true;
                $notification->save();
                return $this->success($notification);
            }
            return $this->fail("This notification belongs to user id " . $notification['user_id'] . ' while your id is ' . $user->user_id);
        } catch (\Throwable $th) {
            return $this->fail("Cannot find notification with id " . $validated['notification_id'], 404);
        }
    }
}
