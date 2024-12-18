<?php

namespace App\Notifications;

use App\Enums;
use App\Models\FinalYearInternshipAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected FinalYearInternshipAgreement $agreement) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $period = $this->agreement->starting_at && $this->agreement->ending_at
            ? $this->agreement->starting_at->format('d/m/Y') . ' - ' . $this->agreement->ending_at->format('d/m/Y')
            : __('Not specified');

        $message = (new MailMessage)
            ->subject(__('New Agreement Assignment - :program - :student (:PfeId)', [
                'program' => $this->agreement->student->program->value,
                'student' => $this->agreement->student->name,
                'PfeId' => $this->agreement->student->id_pfe,
            ]))
            ->greeting(__('Hello!'))
            ->line('') // Empty line for spacing
            ->line('### ' . __('Agreement Assignment Notification'))
            ->line(__('An agreement for **:name** has been assigned to **:department** department.', [
                'name' => $this->agreement->student->name,
                'department' => $this->agreement->assigned_department->value,
            ]))
            ->line('') // Empty line for spacing
            ->line('### ' . __('Project Details:'))
            ->line('- **' . __('PFE ID:') . '** ' . $this->agreement->student->id_pfe)
            ->line('- **' . __('Title:') . '** ' . $this->agreement->title)
            ->line('- **' . __('Organization:') . '** ' . $this->agreement->organization->name)
            ->line('- **' . __('Period:') . '** ' . $period)
            ->action(
                __('View Agreement'),
                route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $this->agreement])
            );

        // Only add the projects dashboard info for department heads
        if ($notifiable->role === Enums\Role::DepartmentHead) {
            $message->line('') // Empty line for spacing
                ->line('### ' . __('Next Steps:'))
                ->line(__('You can now proceed with assigning supervisors and reviewers to this project.'))
                ->action(
                    __('Go to Projects Dashboard'),
                    route('filament.Administration.pages.projects-dashboard')
                );
        }

        return $message
            ->line('') // Empty line for spacing
            ->line('---')
            ->line(__('You are receiving this notification because you are an administrator or department head.'))
            ->line(__('This is an automated notification.'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('New Agreement Assignment'),
            'body' => __('An agreement for :name has been assigned to :department department.', [
                'name' => $this->agreement->student->name,
                'department' => $this->agreement->assigned_department->value,
            ]),
            'action' => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $this->agreement]),
        ];
    }
}
