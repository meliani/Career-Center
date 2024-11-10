<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendApplicationsNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected $internship,
        protected $emailBody
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Applications for :title', ['title' => $this->internship->project_title]))
            ->view('emails.raw', ['content' => $this->emailBody]);
    }
}
