<?php

namespace App\Notifications;

use App\Enums\Role;
use App\Models\FinalYearInternshipAgreement;
use App\Models\User;
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

        $systemUsers = User::where('role', Role::System)->pluck('email')->toArray();

        return (new MailMessage)
            ->cc($systemUsers)
            ->subject(__('New Final Year Internship Agreement - :program - :student (:PfeId)', [
                'program' => $this->agreement->student->program->value,
                'student' => $this->agreement->student->name,
                'PfeId' => $this->agreement->student->id_pfe,
            ]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->formal_name]))
            ->line('') // Empty line for spacing
            ->line(__('Student **:name** from **:program** program has submitted a new internship agreement.', [
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
                route('filament.Administration.home')
            )
            ->line('') // Empty line for spacing
            ->line('### ' . __('Required Action:'))
            ->line(__('The agreement is now ready for department assignment through careers platform.'))
            ->line('') // Empty line for spacing
            ->line('---')
            ->line(__('You are receiving this notification as the coordinator of the :program program.', [
                'program' => $this->agreement->student->program->getLabel(),
            ]))
            ->line(__('This is an automated notification.'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('New Final Year Internship Agreement'),
            'body' => __('Student :name from :program program has submitted a new internship agreement.', [
                'name' => $this->agreement->student->name,
                'program' => $this->agreement->student->program->getLabel(),
            ]),
            'action' => route('filament.Administration.pages.projects-dashboard'),
        ];
    }
}
