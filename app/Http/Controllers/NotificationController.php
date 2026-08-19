<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(DatabaseNotification $notification)
    {
        if ((string) $notification->notifiable_id === (string) Auth::id()) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
}