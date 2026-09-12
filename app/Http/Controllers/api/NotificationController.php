<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //all notifications and count
    public function index(Request $request)
{
    $user = $request->user();

    return response()->json([
        'unread_count'  => $user->unreadNotifications()->count(), // Badge count
        'notifications' => $user->notifications()->latest()->get(), // Full list (newest first)
    ]);
}

//mark all as read
public function markAllAsRead(Request $request)
{
    // Sets read_at = now() for all unread notifications of this user at once
    $request->user()->unreadNotifications->markAsRead();

    return response()->json([
        'message' => 'All notifications marked as read',
        'unread_count' => 0
    ]);
}
// delete notification
public function destroy(Request $request, string $id)
{
    // Find the notification ONLY if it belongs to the authenticated user
    $notification = $request->user()->notifications()->findOrFail($id);

    $notification->delete();

    return response()->json([
        'message' => 'Notification deleted successfully',
        'unread_count' => $request->user()->unreadNotifications()->count()
    ]);
}
}
