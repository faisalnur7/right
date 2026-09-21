<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function redirect($id)
    {
        $notification = Notification::where(function ($q) {
            $q->where('is_for_admin', 1);
        })->findOrFail($id);

        // Mark as read
        $notification->update(['is_read_by_admin' => 1]);

        // Redirect to route if exists
        if ($notification->route && \Route::has($notification->route)) {
            return redirect()->route($notification->route, $notification->data ?? []);
        }

        // Fallback
        return redirect()->back();
    }
}