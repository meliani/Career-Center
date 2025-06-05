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
            ->subject(__('The Defense :id has been authorized', ['id' => $this->project->id_pfe]))
            
            // Main heading
            ->line(__('# The Defense :id has been authorized.', ['id' => $this->project->id_pfe]))
            ->line('---')
            
            // Defense overview
            ->line(__('**Administrative supervisor:** ') . $this->project->administrative_supervisor)
            ->line(__('**Defense Date:** ') . $this->project->defense_plan)
            ->line('---')
            
            // Student information section
            ->line(__('## Student information'))
            ->line(__('**Name:** ') . ($this->project->students ? $this->project->students->implode('full_name', ' & ') : 'Undefined'))
            ->line(__('**ID PFE:** ') . $this->project->id_pfe)
            ->line(__('**Program:** ') . ($this->project->students ? $this->project->students->map(function ($student) {
                return $student->program->value;
            })->implode(' & ') : 'Undefined'))
            ->line(__('**Organization:** ') . $this->project->organization_name)
            ->line('---')
            
            // Project information section
            ->line(__('## Project information'))
            ->line(__('**Title:** ') . $this->project->title)
            ->line('---')
            
            // Supervisors section
            ->line(__('## Supervisors'))
            ->line(__('**Academic supervisor:** ') . $this->project->academic_supervisor_name)
            ->line(__('**External supervisor:** ') . $this->project->external_supervisor_name)
            ->line('---')
            
            // Reviewers section
            ->line(__('## Reviewers'))
            ->line(__('**Reviewer 1:** ') . $this->project->reviewer1)
            ->line(__('**Reviewer 2:** ') . $this->project->reviewer2)
            ->line('---')
            
            // Organization evaluation sheet (conditional)
            ->lineIf($this->project->organization_evaluation_sheet_url, __('## Organization Evaluation sheet'))
            ->lineIf($this->project->organization_evaluation_sheet_url, __('You can download the organization evaluation sheet from the link below.'))
            ->lineIf($this->project->organization_evaluation_sheet_url, __('[Download Organization Evaluation Sheet](:url)', ['url' => $this->project->organization_evaluation_sheet_url]))
            ->lineIf($this->project->organization_evaluation_sheet_url, '---')
            
            // Jury evaluation sheet section
            ->line(__('## Jury Evaluation sheet'))
            ->line(__('You can download the evaluation sheet from the link below.'))
            ->line(__('[Download Evaluation Sheet](:url)', ['url' => $this->project->evaluation_sheet_url]))
            ->line('---')
            
            // Action button and closing
            ->action(__('View Planning'), url('https://carrieres.inpt.ac.ma/soutenances'))
            ->line('---')
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
