<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class KYCSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('admin.verifications.show', $this->user->uid);

        return (new MailMessage)
            ->subject('New KYC Verification Request - ' . config('app.name'))
            ->greeting('Hello Admin,')
            ->line('A new user has submitted documents for KYC verification.')
            ->line('User Name: ' . $this->user->displayName())
            ->line('User Email: ' . $this->user->email)
            ->action('Review Documents', $url)
            ->line('Please review and process the request as soon as possible.');
    }
}
