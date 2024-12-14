<?php

namespace App\Notifications;

use App\Models\CollaborationRequest;
use App\Models\Project;
use App\Models\Year;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
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
        // Get the project through the sender's agreement
        $project = Project::query()
            ->whereHas('agreements', function (Builder $query) {
                $query->whereHas('agreeable', function (Builder $query) {
                    $query->where('student_id', $this->collaborationRequest->sender_id)
                        ->where('year_id', Year::current()->id);
                });
            })
            ->first();

        return (new MailMessage)
            ->subject(__('Collaboration Request Accepted'))
            ->greeting(__('Hello :name', ['name' => $notifiable->first_name]))
            ->line(__(':student has accepted your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]))
            ->line(__('You are now collaborating on the same project.'))
            ->when($project, function (MailMessage $message) use ($project) {
                return $message->action(__('View Project'), route('filament.app.resources.projects.view', [
                    'record' => $project->id,
                ]));
            })
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
        // Get the project through the sender's agreement
        $project = Project::query()
            ->whereHas('agreements', function (Builder $query) {
                $query->whereHas('agreeable', function (Builder $query) {
                    $query->where('student_id', $this->collaborationRequest->sender_id)
                        ->where('year_id', Year::current()->id);
                });
            })
            ->first();

        $notification = FilamentNotification::make()
            ->title(__('Collaboration Request Accepted'))
            ->success()
            ->body(__(':student has accepted your collaboration request.', [
                'student' => $this->collaborationRequest->receiver->full_name,
            ]));

        if ($project) {
            $notification->actions([
                Action::make('view')
                    ->label(__('View Project'))
                    ->url(route('filament.app.resources.projects.view', [
                        'record' => $project->id,
                    ])),
            ]);
        }

        return $notification;
    }
}
