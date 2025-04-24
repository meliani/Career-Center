<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class SendStudentsInfoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Collection
     */
    public $students;

    /**
     * @var string
     */
    public $emailBody;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
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
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.students-info', [
                'content' => $this->emailBody,
                'url' => $this->url,
                'count' => $this->students->count()
            ]);
    }
}
