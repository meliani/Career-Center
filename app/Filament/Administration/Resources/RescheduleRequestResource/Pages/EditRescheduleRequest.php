<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Pages;

use App\Filament\Administration\Resources\RescheduleRequestResource;
use App\Models\RescheduleRequest;
use App\Enums\RescheduleRequestStatus;
use App\Services\DefenseReschedulingService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditRescheduleRequest extends EditRecord
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_details')
                ->label('View Full Details')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->modalHeading(fn (): string => 'Reschedule Request #' . $this->record->id)
                ->modalContent(fn () => view('filament.administration.reschedule-request-details', ['record' => $this->record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
                
            Actions\Action::make('check_availability')
                ->label('Check Availability')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->modalHeading('Availability Check')
                ->modalContent(fn () => view('filament.administration.availability-check', ['record' => $this->record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending),
                
            Actions\Action::make('approve')
                ->label('Approve Request')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Reschedule Request')
                ->modalDescription(fn (): string => 
                    "Are you sure you want to approve this request and reschedule {$this->record->student->full_name}'s defense?"
                )
                ->modalSubmitActionLabel('Yes, Approve')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending)
                ->action(function () {
                    $this->processRequest(RescheduleRequestStatus::Approved, 'Request approved from edit page.');
                }),
                
            Actions\Action::make('reject')
                ->label('Reject Request')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->placeholder('Please provide a detailed reason for rejecting this request'),
                ])
                ->modalHeading('Reject Reschedule Request')
                ->modalSubmitActionLabel('Reject Request')
                ->visible(fn () => $this->record->status === RescheduleRequestStatus::Pending)
                ->action(function (array $data) {
                    $this->processRequest(RescheduleRequestStatus::Rejected, $data['rejection_reason']);
                }),
                
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
            
            // Redirect back to list
            $this->redirect($this->getResource()::getUrl('index'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error Processing Request')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Reschedule request updated successfully.';
    }
}
