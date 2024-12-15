<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborationRequestRejected extends Notification implements ShouldQueue
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
            ->subject(__('Collaboration Request Rejected'))
            ->greeting(__('Hello :name', ['name' => $notifiable->first_name]))
            ->line(__(':student has rejected your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->line(__('You can send a new collaboration request to another student.'))
            ->action(__('Find Collaborators'), route('filament.app.pages.welcome-dashboard'))
            ->line(__('Thank you for using our application!'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => __('Collaboration Request Rejected'),
            'message' => __(':student has rejected your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]),
        ];
    }

    public function toFilament($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Collaboration Request Rejected'))
            ->warning()
            ->body(__(':student has rejected your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->actions([
                Action::make('view')
                    ->label(__('Find Another Collaborator'))
                    ->url(route('filament.app.pages.welcome-dashboard')),
            ]);
    }

    public function toAdminFilament($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Collaboration Request Rejected'))
            ->warning()
            ->body(__(':receiver has rejected collaboration request from :sender', [
                'receiver' => $this->collaborationRequest->receiver->full_name,
                'sender' => $this->collaborationRequest->sender->full_name,
            ]));
    }
}
