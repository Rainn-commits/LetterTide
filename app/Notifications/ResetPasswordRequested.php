<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPasswordRequested extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => 'Permintaan Reset Password',
            'message' => "{$this->user->name} ({$this->user->email}) mengajukan permintaan reset password.",
            'url'     => route('dashboard'),
        ];
    }
}
