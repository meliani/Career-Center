<?php

namespace App\Notifications;

use App\Models\FinalYearInternshipAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementCancellationNotification extends Notification
{
    use Queueable;

    protected $agreement;

    public function __construct(FinalYearInternshipAgreement $agreement)
    {
        $this->agreement = $agreement;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Internship Agreement Cancelled')
            ->line('Your internship agreement has been cancelled.')
            ->line($this->agreement->cancellation_reason
                ? 'Reason: ' . $this->agreement->cancellation_reason
                : 'No reason provided.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Your internship agreement has been cancelled.',
            'reason' => $this->agreement->cancellation_reason,
            'agreement_id' => $this->agreement->id,
        ];
    }
}
