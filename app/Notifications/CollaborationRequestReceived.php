<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborationRequestReceived extends Notification implements ShouldQueue
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
            ->subject(__('New Collaboration Request'))
            ->greeting(__('Hello :name', ['name' => $notifiable->first_name]))
            ->line(__(':student would like to collaborate with you on their project.', [
                'student' => $this->collaborationRequest->sender->full_name,
            ]))
            ->line(__('Message: ":message"', [
                'message' => $this->collaborationRequest->message,
            ]))
            ->action(__('View Request'), route('filament.app.pages.welcome-dashboard'))
            ->line(__('Please login to your dashboard to accept or reject this request.'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => __('New Collaboration Request'),
            'message' => __(':student would like to collaborate with you.', [
                'student' => $this->collaborationRequest->sender->full_name,
            ]),
        ];
    }

    public function toFilament($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('New Collaboration Request'))
            ->info()
            ->body(__(':student would like to collaborate with you: ":message"', [
                'student' => $this->collaborationRequest->sender->full_name,
                'message' => $this->collaborationRequest->message,
            ]))
            ->actions([
                Action::make('view')
                    ->label(__('View Request'))
                    ->url(route('filament.app.pages.welcome-dashboard')),
            ]);
    }

    /**
     * Get the notification for administrators
     */
    public function toAdminMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New Student Collaboration Request'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('A new collaboration request has been created:'))
            ->line(__('From: :sender', ['sender' => $this->collaborationRequest->sender->full_name]))
            ->line(__('To: :receiver', ['receiver' => $this->collaborationRequest->receiver->full_name]))
            ->line(__('Message: ":message"', [
                'message' => $this->collaborationRequest->message,
            ]))
            ->action(__('View Details'), route('filament.admin.pages.dashboard'))
            ->line(__('This is an automated notification.'));
    }

    public function toAdminFilament($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('New Student Collaboration Request'))
            ->info()
            ->body(__(':sender wants to collaborate with :receiver', [
                'sender' => $this->collaborationRequest->sender->full_name,
                'receiver' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->actions([
                Action::make('view')
                    ->label(__('View Details'))
                    ->url(route('filament.admin.pages.dashboard')),
            ]);
    }
}
