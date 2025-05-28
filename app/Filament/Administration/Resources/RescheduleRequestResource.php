<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\RescheduleRequestResource\Pages;
use App\Models\RescheduleRequest;
use App\Enums\RescheduleRequestStatus;
use App\Services\DefenseReschedulingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class RescheduleRequestResource extends Resource
{
    protected static ?string $model = RescheduleRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Defense Rescheduling';

    protected static ?string $navigationGroup = 'Defense Management';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Request ID')
                            ->disabled(),
                            
                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'id')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->disabled(),
                            
                        Forms\Components\Select::make('timetable_id')
                            ->relationship('timetable', 'id')
                            ->disabled(),
                            
                        Forms\Components\Select::make('status')
                            ->options(RescheduleRequestStatus::class)
                            ->required(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Preferred New Schedule')
                    ->schema([
                        Forms\Components\Select::make('preferred_timeslot_id')
                            ->relationship('preferredTimeslot', 'id')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->start_time->format('l, F j, Y') . ' - ' . 
                                    $record->start_time->format('H:i') . ' to ' . 
                                    $record->end_time->format('H:i');
                            })
                            ->required()
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Student Reason')
                            ->disabled()
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('Add notes about why the request was approved or rejected')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('Processing Information')
                    ->schema([
                        Forms\Components\Select::make('processed_by')
                            ->relationship('processor', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (RescheduleRequestStatus $state): string => match ($state) {
                        RescheduleRequestStatus::Pending => 'warning',
                        RescheduleRequestStatus::Approved => 'success',
                        RescheduleRequestStatus::Rejected => 'danger',
                    })
                    ->formatStateUsing(fn (RescheduleRequestStatus $state): string => ucfirst($state->value))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested On')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('preferredTimeslot.start_time')
                    ->label('Requested Date & Time')
                    ->formatStateUsing(function ($record) {
                        if (!$record->preferredTimeslot) {
                            return 'N/A';
                        }
                        return $record->preferredTimeslot->start_time->format('F j, Y') . ' - ' . 
                            $record->preferredTimeslot->start_time->format('H:i') . ' to ' . 
                            $record->preferredTimeslot->end_time->format('H:i');
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed On')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(RescheduleRequestStatus::class),
                    
                Tables\Filters\Filter::make('pending')
                    ->label('Pending Requests')
                    ->query(fn (Builder $query): Builder => $query->where('status', RescheduleRequestStatus::Pending))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Process'),
                    
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Reschedule Request')
                    ->modalDescription('Are you sure you want to approve this defense rescheduling request? This will reschedule the student\'s defense to their preferred timeslot.')
                    ->modalSubmitActionLabel('Yes, Approve Request')
                    ->visible(fn (RescheduleRequest $record) => $record->status === RescheduleRequestStatus::Pending)
                    ->action(function (RescheduleRequest $record) {
                        try {
                            DB::beginTransaction();
                            
                            // Update the request status
                            $record->update([
                                'status' => RescheduleRequestStatus::Approved,
                                'processed_by' => auth()->id(),
                                'processed_at' => now(),
                                'admin_notes' => 'Request approved. The defense has been rescheduled as requested.',
                            ]);
                            
                            // Use the service to reschedule the defense
                            $reschedulingService = new DefenseReschedulingService();
                            $newTimetable = $reschedulingService->rescheduleDefense($record);
                            
                            if (!$newTimetable) {
                                throw new \Exception('Failed to reschedule the defense. Please try again or check the system logs.');
                            }
                            
                            DB::commit();
                            
                            // Send notification to the student
                            Notification::make()
                                ->title('Defense Rescheduled Successfully')
                                ->body("The defense has been rescheduled to {$newTimetable->timeslot->start_time->format('F j, Y')} at {$newTimetable->timeslot->start_time->format('H:i')} in room {$newTimetable->room->name}.")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            DB::rollBack();
                            
                            Notification::make()
                                ->title('Error Rescheduling Defense')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Reschedule Request')
                    ->modalDescription('Are you sure you want to reject this defense rescheduling request? The student will be notified and will need to submit a new request if they still wish to reschedule.')
                    ->modalSubmitActionLabel('Yes, Reject Request')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Please provide a reason for rejecting this request'),
                    ])
                    ->visible(fn (RescheduleRequest $record) => $record->status === RescheduleRequestStatus::Pending)
                    ->action(function (RescheduleRequest $record, array $data) {
                        try {
                            $record->update([
                                'status' => RescheduleRequestStatus::Rejected,
                                'processed_by' => auth()->id(),
                                'processed_at' => now(),
                                'admin_notes' => $data['rejection_reason'],
                            ]);
                            
                            // TODO: Send notification to student about rejection
                            
                            Notification::make()
                                ->title('Request Rejected')
                                ->body('The rescheduling request has been rejected and the student has been notified.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error Rejecting Request')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRescheduleRequests::route('/'),
            'edit' => Pages\EditRescheduleRequest::route('/{record}/edit'),
        ];
    }    
}
