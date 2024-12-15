<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminCollaborationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected CollaborationRequest $request,
        protected string $type // 'request', 'accepted', or 'rejected'
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('Final Year Project Collaboration Update'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]));

        switch ($this->type) {
            case 'request':
                $message->line(__('New collaboration request initiated:'))
                    ->line(__('• From: :sender (:sender_id)', [
                        'sender' => $this->request->sender->full_name,
                        'sender_id' => $this->request->sender->id_pfe,
                    ]))
                    ->line(__('• To: :receiver (:receiver_id)', [
                        'receiver' => $this->request->receiver->full_name,
                        'receiver_id' => $this->request->receiver->id_pfe,
                    ]))
                    ->line(__('Message: ":message"', ['message' => $this->request->message]));

                break;

            case 'accepted':
                $message->line(__('A new project team has been formed:'))
                    ->line(__('• :sender (:sender_id)', [
                        'sender' => $this->request->sender->full_name,
                        'sender_id' => $this->request->sender->id_pfe,
                    ]))
                    ->line(__('• :receiver (:receiver_id)', [
                        'receiver' => $this->request->receiver->full_name,
                        'receiver_id' => $this->request->receiver->id_pfe,
                    ]));

                break;

            case 'rejected':
                $message->line(__('Collaboration request rejected:'))
                    ->line(__(':receiver has rejected the collaboration request from :sender', [
                        'receiver' => $this->request->receiver->full_name,
                        'sender' => $this->request->sender->full_name,
                    ]));

                break;
        }

        return $message
            ->action(__('View Details'), route('filament.Administration.resources.projects.index'))
            ->line(__('This is an automated administrative notification.'));
    }

    public function toDatabase($notifiable): array
    {
        $message = match ($this->type) {
            'request' => __(':sender is requesting to collaborate with :receiver', [
                'sender' => $this->request->sender->full_name,
                'receiver' => $this->request->receiver->full_name,
            ]),
            'accepted' => __('New project team formed: :sender and :receiver', [
                'sender' => $this->request->sender->full_name,
                'receiver' => $this->request->receiver->full_name,
            ]),
            'rejected' => __(':receiver rejected collaboration with :sender', [
                'receiver' => $this->request->receiver->full_name,
                'sender' => $this->request->sender->full_name,
            ]),
        };

        return [
            'title' => __('Project Collaboration Update'),
            'message' => $message,
        ];
    }

    public function toFilament($notifiable): FilamentNotification
    {
        $notification = FilamentNotification::make()
            ->title(__('Project Collaboration Update'))
            ->icon('heroicon-o-user-group')
            ->duration(10000);

        switch ($this->type) {
            case 'request':
                $notification->info()
                    ->body(__('New request: :sender → :receiver', [
                        'sender' => $this->request->sender->full_name,
                        'receiver' => $this->request->receiver->full_name,
                    ]));

                break;

            case 'accepted':
                $notification->success()
                    ->body(__('Team formed: :sender + :receiver', [
                        'sender' => $this->request->sender->full_name,
                        'receiver' => $this->request->receiver->full_name,
                    ]));

                break;

            case 'rejected':
                $notification->warning()
                    ->body(__(':receiver declined :sender\'s request', [
                        'receiver' => $this->request->receiver->full_name,
                        'sender' => $this->request->sender->full_name,
                    ]));

                break;
        }

        return $notification->actions([
            Action::make('view')
                ->label(__('View Projects'))
                ->url(route('filament.Administration.resources.projects.index')),
        ]);
    }
}
