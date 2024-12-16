<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborationReminderNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Choose Your Collaborator'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Please check the collaboration feature and choose your collaborator for the final year project.'))
            ->action(__('Find Collaborators'), route('filament.app.pages.welcome-dashboard'))
            ->line(__('Thank you.'));
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => __('Choose Your Collaborator'),
            'message' => __('Please check the collaboration feature and choose your collaborator.'),
            'action_url' => route('filament.app.pages.welcome-dashboard'),
        ];
    }
}
