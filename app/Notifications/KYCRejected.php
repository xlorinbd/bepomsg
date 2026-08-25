<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KYCRejected extends Notification
{
    use Queueable;

    protected $reason;

    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Verification Rejected - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->displayName() . ',')
            ->line('We regret to inform you that your document verification was rejected.')
            ->line('Reason: ' . $this->reason)
            ->line('Please log in to your dashboard and resubmit the correct documents for verification.')
            ->action('Resubmit Documents', route('user.home'))
            ->line('If you have any questions, please contact our support team.');
    }
}
