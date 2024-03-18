<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectSupervisorAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;

    public $student;

    public $emailSubject = '';

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, Student $student)
    {
        $this->project = $project;
        $this->student = $student;
        $this->emailSubject = "L'encadrant de votre stage de Projet de Fin d'Études";
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("L'encadrant de votre stage de Projet de Fin d'Études")
            ->greeting("Bonjour {$this->student->long_full_name},")
            ->lineIf($this->project->supervisor(), "Nous vous informons que votre encadrant de stage PFE est **Pr. {$this->project->supervisor()?->full_name}**")
            // ->action(__('Voir le projet'), url('/'))
            ->lineIf($this->project->supervisor(), "N'hésitez pas à contacter votre encadrant par mail ({$this->project->supervisor()?->email}) pour convenir d'un premier rendez-vous et établir ensemble
            les modalités de travail.")
            ->lineIf(! $this->project->supervisor(), "Nous vous informons que votre encadrant pour votre stage de Projet de Fin d'Études n'a pas encore été désigné. Nous vous tiendrons informé dès que cela sera fait.")
            ->line('Pour rappel, voici les informations relatives à votre stage :')
            ->line("Organisation : **{$this->project->organization}**")
            ->line("Titre du PFE : **{$this->project->title}**")
            // ->line("Date de début : {$this->project->start_date}")
            // ->line("Date de fin : {$this->project->end_date}")
            ->lineIf($this->project->hasTeammate(), "Vous êtes en binôme pour ce projet, votre binôme est : **{$this->student->teammate()?->long_full_name}**")
            ->line('---')
            ->salutation("Cordialement,\n\n **La DASRE**");
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
