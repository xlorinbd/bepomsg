<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KYCApproved extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Verification Approved - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->displayName() . ',')
            ->line('Congratulations! Your documents have been successfully verified.')
            ->line('Your account is now fully active, and you have complete access to all features of the platform.')
            ->action('Go to Dashboard', route('user.home'))
            ->line('Thank you for choosing ' . config('app.name') . '!');
    }
}
