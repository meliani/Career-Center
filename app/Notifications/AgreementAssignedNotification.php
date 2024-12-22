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
        protected ?object $triggeredBy = null
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

        $message = (new MailMessage)
            ->cc($systemUsers)
            ->subject(__('New Final Project Assigned - :program - :student (:PfeId)', [
                'program' => $this->agreement->student->program->value,
                'student' => $this->agreement->student->name,
                'PfeId' => $this->agreement->student->id_pfe,
            ]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->formal_name]))
            ->line('') // Empty line for spacing
            ->line(__('The program coordinator of **:program**, **:coordinator**, has assigned the project \'**:title**\' to your department **:department**.', [
                'program' => $this->agreement->student->program->value,
                'coordinator' => $this->triggeredBy?->formal_name ?? __('System'),
                'title' => $this->agreement->title,
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
                ->line(__('The project is now ready for supervisor and reviewer assignments through careers platform.'))
                ->action(
                    __('Go to Projects Dashboard'),
                    route('filament.Administration.pages.projects-dashboard')
                );
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
        return [
            'title' => __('New Agreement Assignment'),
            'body' => __('The program coordinator of :program, :coordinator, has assigned the project \':title\' to your department :department.', [
                'program' => $this->agreement->student->program->value,
                'coordinator' => $this->triggeredBy?->formal_name ?? __('System'),
                'title' => $this->agreement->title,
                'department' => $this->agreement->assigned_department->value,
            ]),
            'action' => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $this->agreement]),
        ];
    }
}
