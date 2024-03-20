<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $project = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
            ->subject('Project modifié')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Le projet (**{$this->project->id_pfe}**) portant le titre : **{$this->project->title}** a été modifié.")
            ->line("Pour plus d'informations, veuillez consulter le projet en cliquant sur le lien ci-dessous.")
            ->action('Consulter le projet', \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$this->project->id]))
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
