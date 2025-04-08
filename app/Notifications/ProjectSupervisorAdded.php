<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Student;
use Carbon\Carbon;
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
        // Get academic supervisor info
        $supervisorName = $this->project->academic_supervisor() ?
            'M./Mme ' . $this->project->academic_supervisor()->name :
            'votre encadrant(e) (pas encore assigné(e))';

        $supervisorEmail = $this->project->academic_supervisor() ?
            $this->project->academic_supervisor()->email :
            '';

        // Calculate mid-term deadline as the halfway point between start and end dates
        $midTermDeadline = 'à définir';

        if ($this->project->start_date && $this->project->end_date) {
            $startDate = Carbon::parse($this->project->start_date);
            $endDate = Carbon::parse($this->project->end_date);
            $totalDays = $startDate->diffInDays($endDate);
            $midPointDays = floor($totalDays / 2);
            $midTermDeadline = $startDate->copy()->addDays($midPointDays)->format('d/m/Y');
        }

        return (new MailMessage)
            ->subject("L'encadrant de votre stage de Projet de Fin d'Études")
            ->greeting('Cher(e) étudiant(e),')
            ->line("Suite à la validation de l'étape liée au dépôt et à la signature de votre convention de stage, et en accord avec le règlement intérieur de l'INPT, nous portons à votre connaissance que {$supervisorName} a été nommé(e) comme votre encadrant(e) interne pour votre projet intitulé : **{$this->project->title}**.")
            ->when($supervisorEmail, function ($message) use ($supervisorEmail) {
                return $message->line("Afin d'assurer un suivi optimal de votre travail, nous vous encourageons à prendre contact avec votre encadrant(e), si cela n'a pas encore été fait, à l'adresse suivante : **{$supervisorEmail}**.");
            })
            ->line("Par ailleurs, nous vous rappelons que la date limite de soumission de votre rapport mi-parcours est fixée au **{$midTermDeadline}**. Ce document devra être envoyé par mail à votre encadrant en copiant **entreprises@inpt.ac.ma**")
            ->line('Nous restons à votre disposition pour toute information complémentaire.')
            ->salutation("Cordialement,\nDASRE INPT");
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
