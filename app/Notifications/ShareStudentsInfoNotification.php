<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShareStudentsInfoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $students;
    public $emailBody;
    public $subject;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $students, string $emailBody, string $subject, string $url)
    {
        $this->students = $students;
        $this->emailBody = $emailBody;
        $this->subject = $subject;
        $this->url = $url;
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
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello!')
            ->line($this->emailBody);
            
        // Don't add the action button if the email body already contains the URL
        if (!str_contains($this->emailBody, $this->url)) {
            $mail->action('View Student Information', $this->url);
        }
            
        $mail->line('This link will expire in 7 days.');
            
        return $mail;
    }
}
