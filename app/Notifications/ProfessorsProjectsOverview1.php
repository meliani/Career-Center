<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfessorsProjectsOverview extends Notification implements ShouldQueue
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
        $this->emailSubject = "Vos encadrement de stages de Projet de Fin d'Études";
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
        // dd($this->project);
        //

        return (new MailMessage)
            ->subject("Vos encadrement de stages de Projet de Fin d'Études");
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
