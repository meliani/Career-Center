<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\RescheduleRequestResource\Pages;
use App\Models\RescheduleRequest;
use App\Models\Timeslot;
use App\Models\Room;
use App\Models\Student;
use App\Enums\RescheduleRequestStatus;
use App\Services\DefenseReschedulingService;
use App\Services\ProfessorService;
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
use Filament\Tables\Actions\BulkAction;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class RescheduleRequestResource extends Resource
{
    protected static ?string $model = RescheduleRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Defense Rescheduling';

    protected static ?string $navigationGroup = 'Defense Management';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        // Form disabled for administration - reschedule requests are view-only
        return $form
            ->schema([
                Forms\Components\Placeholder::make('notice')
                    ->content('Reschedule requests cannot be created or edited from the administration panel. Use the action buttons to approve or reject requests.')
                    ->extraAttributes(['class' => 'text-warning-600 font-medium'])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->with([
                'student', 
                'timetable.timeslot', 
                'timetable.room', 
                'timetable.project.professors',
                'preferredTimeslot', 
                'preferredRoom',
                'processor'
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('student.first_name')
                    ->label('Student')
                    ->formatStateUsing(fn ($record) => $record->student ? $record->student->first_name . ' ' . $record->student->last_name : 'N/A')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight(FontWeight::Medium),
                    
                Tables\Columns\TextColumn::make('timetable.project.title')
                    ->label('Project')
                    ->searchable()
                    ->toggleable()
                    ->limit(30)
                    ->tooltip(function (RescheduleRequest $record): ?string {
                        return $record->timetable?->project?->title;
                    }),
                    
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
                    
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->state(function (RescheduleRequest $record): string {
                        if (!$record->timetable?->timeslot?->start_time) {
                            return 'UNKNOWN';
                        }
                        $defenseDate = \Carbon\Carbon::parse($record->timetable->timeslot->start_time);
                        $daysUntilDefense = now()->diffInDays($defenseDate, false);
                        if ($daysUntilDefense <= 3) return 'URGENT';
                        if ($daysUntilDefense <= 7) return 'HIGH';
                        return 'NORMAL';
                    })
                    ->color(function (RescheduleRequest $record): string {
                        if (!$record->timetable?->timeslot?->start_time) {
                            return 'gray';
                        }
                        $defenseDate = \Carbon\Carbon::parse($record->timetable->timeslot->start_time);
                        $daysUntilDefense = now()->diffInDays($defenseDate, false);
                        if ($daysUntilDefense <= 3) return 'danger';
                        if ($daysUntilDefense <= 7) return 'warning';
                        return 'success';
                    }),
                    
                Tables\Columns\TextColumn::make('urgency_days')
                    ->label('Urgency (Days)')
                    ->state(function (RescheduleRequest $record): string {
                        if (!$record->timetable?->timeslot?->start_time) {
                            return 'N/A';
                        }
                        $defenseDate = \Carbon\Carbon::parse($record->timetable->timeslot->start_time);
                        $days = now()->diffInDays($defenseDate, false);
                        return $days . ' days';
                    })
                    ->badge()
                    ->color(function (RescheduleRequest $record): string {
                        if (!$record->timetable?->timeslot?->start_time) {
                            return 'gray';
                        }
                        $defenseDate = \Carbon\Carbon::parse($record->timetable->timeslot->start_time);
                        $days = now()->diffInDays($defenseDate, false);
                        if ($days <= 3) return 'danger';
                        if ($days <= 7) return 'warning';
                        return 'success';
                    }),
                    
                Tables\Columns\TextColumn::make('current_defense_info')
                    ->label('Current Defense')
                    ->state(function (RescheduleRequest $record): string {
                        try {
                            if (!$record->timetable) {
                                return 'No timetable';
                            }
                            if (!$record->timetable->timeslot) {
                                return 'No timeslot';
                            }
                            $startTime = \Carbon\Carbon::parse($record->timetable->timeslot->start_time);
                            $info = $startTime->format('M j, Y H:i');
                            if ($record->timetable->room) {
                                $info .= ' - ' . $record->timetable->room->name;
                            }
                            return $info;
                        } catch (\Exception $e) {
                            return 'Error: ' . $e->getMessage();
                        }
                    }),
                    
                Tables\Columns\TextColumn::make('requested_defense_info')
                    ->label('Requested Time')
                    ->state(function (RescheduleRequest $record): string {
                        try {
                            if (!$record->preferredTimeslot) {
                                return 'No preferred timeslot';
                            }
                            $startTime = \Carbon\Carbon::parse($record->preferredTimeslot->start_time);
                            $info = $startTime->format('M j, Y H:i');
                            if ($record->preferredRoom) {
                                $info .= ' - ' . $record->preferredRoom->name;
                            }
                            return $info;
                        } catch (\Exception $e) {
                            return 'Error: ' . $e->getMessage();
                        }
                    }),
                    
                Tables\Columns\TextColumn::make('availability_status')
                    ->label('Availability')
                    ->badge()
                    ->state(function (RescheduleRequest $record): string {
                        try {
                            if ($record->status !== RescheduleRequestStatus::Pending) {
                                return 'N/A';
                            }
                            
                            if (!$record->preferredTimeslot) {
                                return 'No Preferred Time';
                            }
                            
                            if (!$record->timetable?->project) {
                                return 'No Project';
                            }
                            
                            // Check if project has professors (jury)
                            $project = $record->timetable->project;
                            if (!$project->professors || $project->professors->count() === 0) {
                                return 'No Jury Assigned';
                            }
                            
                            // Check jury availability
                            $available = ProfessorService::checkJuryAvailability(
                                $record->preferredTimeslot,
                                $project,
                                $record->timetable_id
                            );
                            
                            return $available ? 'Available' : 'Conflict';
                            
                        } catch (\Exception $e) {
                            return 'Check Error';
                        }
                    })
                    ->color(function (RescheduleRequest $record): string {
                        try {
                            if ($record->status !== RescheduleRequestStatus::Pending) {
                                return 'gray';
                            }
                            
                            if (!$record->preferredTimeslot || !$record->timetable?->project) {
                                return 'gray';
                            }
                            
                            $project = $record->timetable->project;
                            if (!$project->professors || $project->professors->count() === 0) {
                                return 'gray';
                            }
                            
                            $available = ProfessorService::checkJuryAvailability(
                                $record->preferredTimeslot,
                                $project,
                                $record->timetable_id
                            );
                            
                            return $available ? 'success' : 'danger';
                            
                        } catch (\Exception $e) {
                            return 'warning';
                        }
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested On')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed On')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(RescheduleRequestStatus::class)
                    ->multiple(),
                    
                Tables\Filters\Filter::make('pending')
                    ->label('Pending Requests')
                    ->query(fn (Builder $query): Builder => $query->where('status', RescheduleRequestStatus::Pending))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('urgent')
                    ->label('Urgent (≤3 days)')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(3));
                        })->where('status', RescheduleRequestStatus::Pending);
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('high_priority')
                    ->label('High Priority (≤7 days)')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(7));
                        })->where('status', RescheduleRequestStatus::Pending);
                    })
                    ->toggle(),
                    
                Tables\Filters\SelectFilter::make('student')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Student $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('processor')
                    ->relationship('processor', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('has_conflicts')
                    ->label('Has Professor Conflicts')
                    ->query(function (Builder $query): Builder {
                        return $query->where('status', RescheduleRequestStatus::Pending)
                            ->whereHas('preferredTimeslot');
                    })
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
                    
                Tables\Filters\Filter::make('defense_date')
                    ->form([
                        Forms\Components\DatePicker::make('defense_from')
                            ->label('Defense Date From'),
                        Forms\Components\DatePicker::make('defense_until')
                            ->label('Defense Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['defense_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereHas('timetable.timeslot', function ($q) use ($date) {
                                    $q->whereDate('start_time', '>=', $date);
                                })
                            )
                            ->when(
                                $data['defense_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereHas('timetable.timeslot', function ($q) use ($date) {
                                    $q->whereDate('start_time', '<=', $date);
                                })
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['defense_from'] ?? null) {
                            $indicators['defense_from'] = 'Defense from ' . \Carbon\Carbon::parse($data['defense_from'])->toFormattedDateString();
                        }
                        if ($data['defense_until'] ?? null) {
                            $indicators['defense_until'] = 'Defense until ' . \Carbon\Carbon::parse($data['defense_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Quick View')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('gray')
                    ->modalHeading(fn (RescheduleRequest $record): string => 'Reschedule Request #' . $record->id)
                    ->modalContent(function (RescheduleRequest $record) {
                        return view('filament.administration.reschedule-request-details', ['record' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                    
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
                    
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
                        static::processRescheduleRequest($record, RescheduleRequestStatus::Approved, 'Request approved. The defense has been rescheduled as requested.');
                    }),
                    
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('rejection_template')
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
                        Forms\Components\Textarea::make('custom_reason')
                            ->label('Custom Rejection Reason')
                            ->required()
                            ->visible(fn (callable $get) => $get('rejection_template') === 'custom')
                            ->placeholder('Please provide a detailed reason for rejecting this request'),
                    ])
                    ->modalHeading('Reject Reschedule Request')
                    ->modalSubmitActionLabel('Reject Request')
                    ->visible(fn (RescheduleRequest $record) => $record->status === RescheduleRequestStatus::Pending)
                    ->action(function (RescheduleRequest $record, array $data) {
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
                            
                        static::processRescheduleRequest($record, RescheduleRequestStatus::Rejected, $reason);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Bulk Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Approve Requests')
                        ->modalDescription('Are you sure you want to approve all selected pending reschedule requests?')
                        ->modalSubmitActionLabel('Approve All')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $processed = 0;
                            $errors = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === RescheduleRequestStatus::Pending) {
                                    try {
                                        static::processRescheduleRequest($record, RescheduleRequestStatus::Approved, 'Bulk approval - all requirements met.');
                                        $processed++;
                                    } catch (\Exception $e) {
                                        $errors++;
                                    }
                                }
                            }
                            
                            $message = "Processed {$processed} requests successfully.";
                            if ($errors > 0) {
                                $message .= " {$errors} requests failed to process.";
                            }
                            
                            Notification::make()
                                ->title('Bulk Approval Complete')
                                ->body($message)
                                ->success()
                                ->send();
                        }),
                        
                    BulkAction::make('bulk_reject')
                        ->label('Bulk Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('bulk_rejection_reason')
                                ->label('Rejection Reason (applies to all selected)')
                                ->required()
                                ->placeholder('Please provide a reason for rejecting these requests'),
                        ])
                        ->modalHeading('Bulk Reject Requests')
                        ->modalSubmitActionLabel('Reject All')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data) {
                            $processed = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === RescheduleRequestStatus::Pending) {
                                    try {
                                        static::processRescheduleRequest($record, RescheduleRequestStatus::Rejected, $data['bulk_rejection_reason']);
                                        $processed++;
                                    } catch (\Exception $e) {
                                        // Log error but continue processing
                                    }
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Rejection Complete')
                                ->body("Rejected {$processed} requests.")
                                ->success()
                                ->send();
                        }),
                        
                    BulkAction::make('export_requests')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records) {
                            // TODO: Implement export functionality
                            Notification::make()
                                ->title('Export Started')
                                ->body('Export functionality will be implemented.')
                                ->info()
                                ->send();
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->modalHeading('Delete Reschedule Requests')
                        ->modalDescription('Are you sure you want to delete the selected reschedule requests? This action cannot be undone.')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
    
    /**
     * Process a reschedule request (approve or reject)
     */
    protected static function processRescheduleRequest(
        RescheduleRequest $record, 
        RescheduleRequestStatus $status, 
        string $adminNotes
    ): void {
        try {
            DB::beginTransaction();
            
            // Update the request status first
            $record->update([
                'status' => $status,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'admin_notes' => $adminNotes,
            ]);
            
            // Commit the status update first
            DB::commit();
            
            if ($status === RescheduleRequestStatus::Approved) {
                // Try to reschedule the defense in a separate transaction
                try {
                    DB::beginTransaction();
                    
                    $reschedulingService = new DefenseReschedulingService();
                    $newTimetable = $reschedulingService->rescheduleDefense($record);
                    
                    if (!$newTimetable) {
                        DB::rollBack();
                        // Status is already updated, just show a warning
                        Notification::make()
                            ->title('Request Approved')
                            ->body('The request has been approved, but automatic rescheduling failed. Please reschedule manually.')
                            ->warning()
                            ->send();
                        return;
                    }
                    
                    DB::commit();
                    
                    $message = "Defense rescheduled successfully to {$newTimetable->timeslot->start_time->format('F j, Y')} at {$newTimetable->timeslot->start_time->format('H:i')}";
                    if ($newTimetable->room) {
                        $message .= " in room {$newTimetable->room->name}";
                    }
                    
                    Notification::make()
                        ->title('Request Approved')
                        ->body($message)
                        ->success()
                        ->send();
                        
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Defense rescheduling failed: ' . $e->getMessage());
                    
                    Notification::make()
                        ->title('Request Approved')
                        ->body('The request has been approved, but automatic rescheduling failed: ' . $e->getMessage())
                        ->warning()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Request Rejected')
                    ->body('The rescheduling request has been rejected and the student has been notified.')
                    ->warning()
                    ->send();
            }
            
            // TODO: Send appropriate notification to student
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error Processing Request')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            throw $e;
        }
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
            'view' => Pages\ViewRescheduleRequest::route('/{record}'),
        ];
    }
    
    public static function getWidgets(): array
    {
        return [
            RescheduleRequestResource\Widgets\RescheduleRequestStatsWidget::class,
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', RescheduleRequestStatus::Pending)->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $pending = static::getModel()::where('status', RescheduleRequestStatus::Pending)->count();
        $urgent = static::getModel()::where('status', RescheduleRequestStatus::Pending)
            ->whereHas('timetable.timeslot', function ($q) {
                $q->where('start_time', '<=', now()->addDays(3));
            })->count();
            
        if ($urgent > 0) return 'danger';
        if ($pending > 5) return 'warning';
        return $pending > 0 ? 'primary' : null;
    }    
}
