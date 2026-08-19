<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPasswordRequested extends Notification
{
    use Queueable;

    public function __construct() {}

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => 'Permintaan Reset Password',
            'message' => 'Seorang pengguna mengajukan permintaan reset password.',
            'url'     => route('dashboard'),
        ];
    }
}