<?php

namespace App\Notifications;

use App\Enums;
use App\Enums\Role;
use App\Models\FinalYearInternshipAgreement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected FinalYearInternshipAgreement $agreement,
        protected ?object $triggeredBy = null,
        protected bool $isReassignment = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $period = $this->agreement->starting_at && $this->agreement->ending_at
            ? $this->agreement->starting_at->format('d/m/Y') . ' - ' . $this->agreement->ending_at->format('d/m/Y')
            : __('Not specified');

        $systemUsers = User::where('role', Role::System)->pluck('email')->toArray();

        $subject = $this->isReassignment
            ? __('Project Reassignment Notification - :program - :student (:PfeId)')
            : __('New Final Project Assigned - :program - :student (:PfeId)');

        $message = $this->isReassignment
            ? __('The program coordinator of **:program**, **:coordinator**, has reassigned the project from your department.')
            : __('The program coordinator of **:program**, **:coordinator**, has assigned the project \'**:title**\' to your department **:department**.');

        $message = (new MailMessage)
            ->cc($systemUsers)
            ->subject(__($subject, [
                'program' => $this->agreement->student->program->value,
                'student' => $this->agreement->student->name,
                'PfeId' => $this->agreement->student->id_pfe,
            ]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->formal_name]))
            ->line('') // Empty line for spacing
            ->line(__($message, [
                'program' => $this->agreement->student->program->value,
                'coordinator' => $this->triggeredBy?->formal_name ?? __('System'),
                'title' => $this->agreement->title,
                'department' => $this->agreement->assigned_department->value,
            ]));

        // Add reassignment specific message if applicable
        if ($this->isReassignment) {
            $message->line('')
                ->line(__('This project has been reassigned to another department. You will no longer receive updates about this project.'));
        }

        $message->line('') // Empty line for spacing
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
        if (! $this->isReassignment) {

            if ($notifiable->role === Enums\Role::DepartmentHead) {
                $message->line('') // Empty line for spacing
                    ->line('### ' . __('Next Steps:'))
                    ->line(__('The project is now ready for supervisor and reviewer assignments through careers platform.'))
                    ->action(
                        __('Go to Projects Dashboard'),
                        route('filament.Administration.pages.projects-dashboard')
                    );
            }
        }

        return $message
            ->line('') // Empty line for spacing
            ->line('---')
            ->line(__('You are receiving this notification because you are the department head of **:department**.', [
                'department' => $this->agreement->assigned_department->getDescription(),
            ]))
            ->line(__('This is an automated notification triggered by **:name**\'s action.', [
                'name' => $this->triggeredBy?->formal_name ?? __('the system'),
            ]));
    }

    public function toDatabase(object $notifiable): array
    {
        $title = $this->isReassignment
            ? __('Project Reassignment Notification')
            : __('New Agreement Assignment');

        $body = $this->isReassignment
            ? __('The program coordinator of :program, :coordinator, has reassigned the project \':title\' from your department.')
            : __('The program coordinator of :program, :coordinator, has assigned the project \':title\' to your department :department.');

        return [
            'title' => $title,
            'body' => __($body, [
                'program' => $this->agreement->student->program->value,
                'coordinator' => $this->triggeredBy?->formal_name ?? __('System'),
                'title' => $this->agreement->title,
                'department' => $this->agreement->assigned_department->value,
            ]),
            'action' => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $this->agreement]),
        ];
    }
}
