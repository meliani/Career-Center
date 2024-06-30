<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefenseAuthorizedNotification extends Notification
{
    use Queueable;

    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->template('emails.templates.notification_email')
            ->from(auth()->user()->email, auth()->user()->full_name)
            ->subject(__('Defense Authorized'))
            ->line(__('# The Defense :id has been authorized.', ['id' => $this->project->id_pfe]))
            // Include evaluation sheet data using ->line()
            ->line(__('**Administrative supervisor:** ') . $this->project->administrative_supervisor)
            ->line(__('**Defense Date:** ') . $this->project->defense_plan)
            ->line(__('## Student information'))
            ->line(__('**Name:** ') . $this->project->students->implode('full_name', ' & '))
            ->line(__('**ID PFE:** ') . $this->project->id_pfe)
            ->line(__('**Program:** ') . $this->project->students->map(function ($student) {
                return $student->program->value;
            })->implode(' & '))
            ->line(__('**Organization:** ') . $this->project->organization_name)
            ->line(__('## Project information'))
            ->line(__('**Title:** ') . $this->project->title)
            ->line(__('## Supervisors'))
            ->line(__('**Academic supervisor:** ') . $this->project->academic_supervisor)
            ->line(__('**External supervisor:** ') . $this->project->external_supervisor_name)
            ->line(__('## Reviewers'))
            ->line(__('**Reviewer 1:** ') . $this->project->reviewer1)
            ->line(__('**Reviewer 2:** ') . $this->project->reviewer2)
            ->line(__('## Evaluation sheet'))
            ->line(__('You can download the evaluation sheet from the link below.'))
            ->line(__('[Download Evaluation Sheet](:url)', ['url' => $this->project->evaluation_sheet_url]))

            ->action(__('View Planning'), url('https://carrieres.inpt.ac.ma/soutenances'))
            ->line(__('Regards'))
            ->salutation(auth()->user()->full_name . ' (' . auth()->user()->email . ')');
        // ->attach($this->project->evaluation_sheet_url, [
        //     'as' => 'EvaluationSheet.pdf',
        //     'mime' => 'application/pdf',
        // ]);
        // ->action(__('View Project'), url('/projects/' . $this->project->id));
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
