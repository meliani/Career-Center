<?php

namespace App\Notifications;

use App\Models\ProfessorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectSupervisorCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $professorProject = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(professorProject $professorProject)
    {
        $this->professorProject = $professorProject;
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
            ->subject('Ajout d\'un encadrant de projet')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("**{$this->professorProject->assigned_by->name}** a ajouté l'encadrant **{$this->professorProject->professor->name}** au projet N° {$this->professorProject->project->id_pfe}.")
            ->line("Pour plus d'informations, veuillez consulter le projet en cliquant sur le lien ci-dessous.")
            ->action('Consulter le projet', \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$this->professorProject->project_id]))
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
