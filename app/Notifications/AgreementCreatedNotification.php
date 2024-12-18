<?php

namespace App\Notifications;

use App\Models\FinalYearInternshipAgreement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementCreatedNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject(__('New Internship Agreement - :program - :student (:PfeId)', [
                'program' => $this->agreement->student->program->value,
                'student' => $this->agreement->student->name,
                'PfeId' => $this->agreement->student->id_pfe,
            ]))
            ->greeting(__('Hello!'))
            ->line('') // Empty line for spacing
            ->line('### ' . __('New Internship Agreement'))
            ->line(__('A new internship agreement has been created by **:name** (:program).', [
                'name' => $this->agreement->student->name,
                'program' => $this->agreement->student->program->value,
            ]))
            ->line('') // Empty line for spacing
            ->line('### ' . __('Project Details:'))
            ->line('- **' . __('PFE ID:') . '** ' . $this->agreement->student->id_pfe)
            ->line('- **' . __('Title:') . '** ' . $this->agreement->title)
            ->line('- **' . __('Organization:') . '** ' . $this->agreement->organization->name)
            ->line('- **' . __('Period:') . '** ' . $period)
            ->action(
                __('Go to Projects Dashboard'),
                route('filament.Administration.pages.projects-dashboard')
            )
            ->line('') // Empty line for spacing
            ->line('### ' . __('Next Steps:'))
            ->line(__('Please review and assign a department to this internship agreement.'))
            ->line('') // Empty line for spacing
            ->line('---')
            ->line(__('You are receiving this notification because you are a program coordinator.'))
            ->line(__('This is an automated notification.'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('New Internship Agreement'),
            'body' => __('A new internship agreement has been created by :name (:program).', [
                'name' => $this->agreement->student->name,
                'program' => $this->agreement->student->program->value,
            ]),
            'action' => route('filament.Administration.pages.projects-dashboard'),
        ];
    }
}
