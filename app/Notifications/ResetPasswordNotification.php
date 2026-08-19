<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $expire = config('auth.passwords.users.expire');

        return (new MailMessage)
            ->subject('Atur Ulang Password - '.config('app.name'))
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena kami menerima permintaan pengaturan ulang password untuk akun Anda.')
            ->action('Reset Password', url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line('Link reset password ini akan kedaluwarsa dalam :count menit.', ['count' => $expire])
            ->line('Jika Anda tidak meminta pengaturan ulang password, abaikan email ini.')
            ->salutation('Salam, '.config('app.name'));
    }
}
