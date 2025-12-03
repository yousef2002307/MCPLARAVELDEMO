<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $user = User::find(1);

            $notifications = $user->notifications()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => $user->unreadNotifications->count(),
                'message' => 'Notifications retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only unread notifications.
     */
    public function unread(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $user = $request->user();

            $notifications = $user->unreadNotifications()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => $user->unreadNotifications->count(),
                'message' => 'Unread notifications retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching unread notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching unread notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->findOrFail($id);

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $user->unreadNotifications->count()
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking all notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->findOrFail($id);

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'unread_count' => $user->unreadNotifications->count()
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->readNotifications()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All read notifications deleted successfully',
                'unread_count' => $user->unreadNotifications->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting read notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting read notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_notifications' => $user->notifications->count(),
                    'unread_count' => $user->unreadNotifications->count(),
                    'read_count' => $user->readNotifications->count(),
                ],
                'message' => 'Notification statistics retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching notification stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notification statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
