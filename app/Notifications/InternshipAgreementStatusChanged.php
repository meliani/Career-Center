<?php

namespace App\Notifications;

use App\Models\InternshipAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InternshipAgreementStatusChanged extends Notification implements ShouldQueue
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
    public function toMail($notifiable): MailMessage
    {
        // dd($notifiable);

        return (new MailMessage)
            ->subject('Changement de statut de la convention de stage')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line(__('Le statut de la convention de stage a changé.'))
            ->line("La convention portant le titre : **{$this->internshipAgreement->title}** a changé de statut.")
            ->line("Le nouveau statut de la convention est : **{$this->internshipAgreement->status->getLabel()}**")
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
