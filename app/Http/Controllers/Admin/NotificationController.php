<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Feeds the console header's bell. See App\Support\AdminNotifications for why
 * this is polled rather than pushed.
 */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'count' => AdminNotifications::unreadCount($user),
            'items' => AdminNotifications::items($user),
        ]);
    }

    /** Opening the bell clears the dot for this admin only. */
    public function seen(Request $request): JsonResponse
    {
        AdminNotifications::markSeen($request->user());

        return response()->json(['count' => 0]);
    }
}
