<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Pages;

use App\Filament\Administration\Resources\RescheduleRequestResource;
use App\Enums\RescheduleRequestStatus;
use App\Services\DefenseReschedulingService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ViewRescheduleRequest extends ViewRecord
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('check_availability')
                ->label('Check Availability')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->modalHeading('Availability Check')
                ->modalContent(fn () => view('filament.administration.availability-check', ['record' => $this->record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending),
                
            Actions\Action::make('quick_approve')
                ->label('Quick Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Quick Approve Request')
                ->modalDescription(fn (): string => 
                    "Approve reschedule request from {$this->record->student->full_name}?"
                )
                ->modalSubmitActionLabel('Approve')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending)
                ->action(function () {
                    $this->processRequest(RescheduleRequestStatus::Approved, 'Quick approval from view page.');
                }),
                
            Actions\Action::make('quick_reject')
                ->label('Quick Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Select::make('rejection_template')
                        ->label('Rejection Reason Template')
                        ->options([
                            'professor_conflict' => 'Professor not available at requested time',
                            'room_conflict' => 'Room not available at requested time',
                            'too_close' => 'Request submitted too close to defense date',
                            'invalid_timeslot' => 'Requested timeslot is not valid for defenses',
                            'insufficient_notice' => 'Insufficient notice provided for rescheduling',
                            'custom' => 'Custom reason (specify below)',
                        ])
                        ->required()
                        ->reactive(),
                    \Filament\Forms\Components\Textarea::make('custom_reason')
                        ->label('Custom Rejection Reason')
                        ->required()
                        ->visible(fn (callable $get) => $get('rejection_template') === 'custom')
                        ->placeholder('Please provide a detailed reason for rejecting this request'),
                ])
                ->modalHeading('Quick Reject Request')
                ->modalSubmitActionLabel('Reject Request')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending)
                ->action(function (array $data) {
                    $reasonMap = [
                        'professor_conflict' => 'The requested timeslot conflicts with professor availability. Please select an alternative time.',
                        'room_conflict' => 'The requested room is not available at the specified time. Please choose a different room or time.',
                        'too_close' => 'Rescheduling requests must be submitted at least 48 hours before the defense date.',
                        'invalid_timeslot' => 'The requested timeslot is outside of defense scheduling hours or on a non-working day.',
                        'insufficient_notice' => 'Insufficient notice provided. Please submit rescheduling requests with adequate advance notice.',
                    ];
                    
                    $reason = $data['rejection_template'] === 'custom' 
                        ? $data['custom_reason'] 
                        : $reasonMap[$data['rejection_template']];
                        
                    $this->processRequest(RescheduleRequestStatus::Rejected, $reason);
                }),
                
            Actions\EditAction::make()
                ->label('Edit/Process')
                ->icon('heroicon-o-pencil-square'),
                
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status !== RescheduleRequestStatus::Approved),
        ];
    }
    
    protected function processRequest(RescheduleRequestStatus $status, string $adminNotes): void
    {
        try {
            DB::beginTransaction();
            
            // Update the request
            $this->record->update([
                'status' => $status,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'admin_notes' => $adminNotes,
            ]);
            
            if ($status === RescheduleRequestStatus::Approved) {
                // Use the service to reschedule the defense
                $reschedulingService = new DefenseReschedulingService();
                $newTimetable = $reschedulingService->rescheduleDefense($this->record);
                
                if (!$newTimetable) {
                    throw new \Exception('Failed to reschedule the defense. Please check the system logs.');
                }
                
                $message = "Defense rescheduled successfully to {$newTimetable->timeslot->start_time->format('F j, Y H:i')}";
                if ($newTimetable->room) {
                    $message .= " in {$newTimetable->room->name}";
                }
                
                Notification::make()
                    ->title('Request Approved')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Request Rejected')
                    ->body('The rescheduling request has been rejected.')
                    ->warning()
                    ->send();
            }
            
            DB::commit();
            
            // Refresh the page to show updated status
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error Processing Request')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
