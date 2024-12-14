<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborationRequestAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected CollaborationRequest $collaborationRequest
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Collaboration Request Accepted'))
            ->greeting(__('Hello :name', ['name' => $notifiable->first_name]))
            ->line(__(':student has accepted your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->line(__('You are now collaborating on the same project.'))
            ->action(__('View Project'), route('filament.app.resources.projects.view', [
                'record' => $this->collaborationRequest->receiver->project,
            ]))
            ->line(__('Thank you for using our application!'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => __('Collaboration Request Accepted'),
            'message' => __(':student has accepted your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]),
        ];
    }

    public function toFilament($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Collaboration Request Accepted'))
            ->success()
            ->body(__(':student has accepted your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->actions([
                Action::make('view')
                    ->label(__('View Project'))
                    ->url(route('filament.app.resources.projects.view', [
                        'record' => $this->collaborationRequest->receiver->project,
                    ])),
            ]);
    }
}
