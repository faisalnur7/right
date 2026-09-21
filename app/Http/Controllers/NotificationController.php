<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function redirect($id)
    {
        $notification = Notification::where(function ($q) {
            $q->where('user_id', Auth::id())
              ->orWhereNull('user_id'); // public notifications
        })->findOrFail($id);

        // Mark as read
        $notification->update(['is_read' => 1]);

        // Redirect to route if exists
        if ($notification->route && \Route::has($notification->route)) {
            return redirect()->route($notification->route, $notification->data ?? []);
        }

        // Fallback
        return redirect()->back();
    }
}
