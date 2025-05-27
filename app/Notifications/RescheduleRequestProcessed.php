<?php

namespace App\Notifications;

use App\Models\RescheduleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class RescheduleRequestProcessed extends Notification implements ShouldQueue
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
        $timetable = $this->rescheduleRequest->timetable;
        $project = $timetable->project;
        $isApproved = $this->rescheduleRequest->status->value === 'approved';
        
        $mail = (new MailMessage)
            ->subject($isApproved ? 'Defense Reschedule Request Approved' : 'Defense Reschedule Request Rejected')
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line($isApproved 
                ? 'Your defense reschedule request has been approved.' 
                : 'Unfortunately, your defense reschedule request has been rejected.');
                  if ($isApproved) {
            $mail->line("Your defense will be rescheduled from {$timetable->timeslot->start_time->format('F j, Y')} to a new date based on your preferences.")
                ->line("We will update your schedule soon and notify you of the new date and time.");
        } else {
            $mail->line("Reason: {$this->rescheduleRequest->admin_notes}")
                ->line("Your defense will remain scheduled for {$timetable->timeslot->start_time->format('F j, Y')} at {$timetable->timeslot->start_time->format('H:i')}.");
        }
        
        return $mail
            ->action('View Details', url(route('filament.app.pages.request-defense-reschedule', ['rescheduleRequest' => $this->rescheduleRequest->id])))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->rescheduleRequest->status->value === 'approved';
        $timetable = $this->rescheduleRequest->timetable;
        
        return [            'id' => $this->rescheduleRequest->id,
            'title' => $isApproved ? 'Defense Reschedule Approved' : 'Defense Reschedule Rejected',
            'approved' => $isApproved,
            'admin_notes' => $this->rescheduleRequest->admin_notes,
            'current_date' => $timetable->timeslot->start_time->format('Y-m-d'),
            'preferred_date' => $this->rescheduleRequest->preferredTimeslot->start_time->format('Y-m-d'),
        ];
    }

    /**
     * Send a Filament notification to the student
     */
    public static function sendToStudent(RescheduleRequest $rescheduleRequest): void
    {
        $student = $rescheduleRequest->student;
        $isApproved = $rescheduleRequest->status->value === 'approved';
        
        // Create a Filament notification for the student
        FilamentNotification::make()
            ->title($isApproved ? 'Defense Reschedule Request Approved' : 'Defense Reschedule Request Rejected')
            ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->body($isApproved 
                ? "Your request to reschedule your defense has been approved. Your defense will be rescheduled soon." 
                : "Your request to reschedule your defense has been rejected. Reason: {$rescheduleRequest->admin_notes}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Details')
                    ->url(route('filament.app.pages.request-defense-reschedule', ['rescheduleRequest' => $rescheduleRequest->id]))
                    ->button(),
            ])
            ->color($isApproved ? 'success' : 'danger')
            ->sendToDatabase($student);
            
        // Also send email notification
        $student->notify(new RescheduleRequestProcessed($rescheduleRequest));
    }
}
