<?php

namespace App\Notifications;

use App\Models\InternshipAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InternshipAgreementAnnounced extends Notification implements ShouldQueue
{
    use Queueable;

    public $internshipAgreement = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(InternshipAgreement $internshipAgreement)
    {
        $this->internshipAgreement = $internshipAgreement;
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
            ->subject('Déclaration de convention de stage')
            ->greeting("Bonjour {$notifiable->long_full_name},")
            ->line("Une nouvelle convention de stage a été déclarée, portant le titre : **{$this->internshipAgreement->title}**.")
            ->line("Pour plus d'information, veuillez consulter la convention en cliquant sur le lien ci-dessous.")
            ->action('Consulter la convention', \App\Filament\Administration\Resources\InternshipAgreementResource::getUrl('edit', [$this->internshipAgreement->id]))
            ->line('---')
            ->salutation("Cordialement,\n\n **Plateforme Carrières**");
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
