<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlumniAccountRequestCreated extends Notification
{
    // use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($alumniAccountRequest)
    {
        $this->alumniAccountRequest = $alumniAccountRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // dd($notifiable);

        return (new MailMessage)
            ->line('Your request for an alumni account has been received.')
            ->action('visit the alumni portal', url('/alumni'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
