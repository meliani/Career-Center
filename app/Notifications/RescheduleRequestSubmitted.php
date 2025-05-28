<?php

namespace App\Notifications;

use App\Models\RescheduleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action as NotificationAction;

class RescheduleRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected RescheduleRequest $rescheduleRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(RescheduleRequest $rescheduleRequest)
    {
        $this->rescheduleRequest = $rescheduleRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $student = $this->rescheduleRequest->student;
        $timetable = $this->rescheduleRequest->timetable;
        $project = $timetable->project;
        
        return (new MailMessage)
            ->subject('Defense Reschedule Request')
            ->greeting('Hello!')
            ->line('A student has submitted a request to reschedule their defense.')            ->line("Student: {$student->full_name}")
            ->line("Project: {$project->title}")
            ->line("Current Date: {$timetable->timeslot->start_time->format('F j, Y')}")
            ->line("Current Time: {$timetable->timeslot->start_time->format('H:i')} - {$timetable->timeslot->end_time->format('H:i')}")
            ->line("Requested Date: {$this->rescheduleRequest->preferredTimeslot->start_time->format('F j, Y')}")
            ->line("Requested Time: {$this->rescheduleRequest->preferredTimeslot->start_time->format('H:i')} - {$this->rescheduleRequest->preferredTimeslot->end_time->format('H:i')}")
            ->line("Reason: {$this->rescheduleRequest->reason}")
            ->action('Review Request', url(route('filament.Administration.resources.reschedule-requests.edit', $this->rescheduleRequest)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $student = $this->rescheduleRequest->student;
        $timetable = $this->rescheduleRequest->timetable;
          return [
            'id' => $this->rescheduleRequest->id,
            'title' => 'Defense Reschedule Request',
            'student_name' => $student->full_name,
            'student_id' => $student->id,
            'current_date' => $timetable->timeslot->start_time->format('Y-m-d'),
            'preferred_date' => $this->rescheduleRequest->preferredTimeslot->start_time->format('Y-m-d'),
            'preferred_time' => $this->rescheduleRequest->preferredTimeslot->start_time->format('H:i'),
        ];
    }    /**
     * Send a Filament notification to admins
     */
    public static function sendToAdmins(RescheduleRequest $rescheduleRequest): void
    {
        $student = $rescheduleRequest->student;
        $timetable = $rescheduleRequest->timetable;
        
        // Get admin users using proper role checking
        $admins = \App\Models\User::whereIn('role', \App\Enums\Role::getAdministratorRoles())->get();
        
        // Create a Filament notification for all admin users
        FilamentNotification::make()
            ->title('New Defense Reschedule Request')
            ->icon('heroicon-o-calendar')
            ->body("Student {$student->full_name} has requested to reschedule their defense from {$timetable->timeslot->start_time->format('F j')} to {$rescheduleRequest->preferredTimeslot->start_time->format('F j')}.")
            ->actions([
                NotificationAction::make('view')
                    ->label('View Request')
                    ->url(route('filament.Administration.resources.reschedule-requests.edit', $rescheduleRequest))
                    ->button(),
            ])
            ->danger()
            ->sendToDatabase($admins);
            
        // Also send email notifications
        $admins->each(function ($admin) use ($rescheduleRequest) {
            $admin->notify(new RescheduleRequestSubmitted($rescheduleRequest));
        });
    }
}
