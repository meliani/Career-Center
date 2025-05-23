<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use App\Models\Project;
use App\Models\Timetable;
use App\Models\Timeslot;
use App\Models\Room;
use App\Services\ProfessorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimetableRelationManager extends RelationManager
{
    // use \Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

    protected static string $relationship = 'timetable';

    protected static bool $isLazy = false;
    
    protected function checkProfessorsAvailability(array $data, $action, $timetableId = null)
    {
        $timeslot = \App\Models\Timeslot::find($data['timeslot_id']);
        $project = $this->getOwnerRecord();
        $errors = [];
        $formHasErrors = false;
        
        // Check if the timeslot and room combination is already taken by another project
        $existingTimetable = \App\Models\Timetable::where('timeslot_id', $data['timeslot_id'])
            ->where('room_id', $data['room_id'])
            ->when($timetableId, fn($query) => $query->where('id', '!=', $timetableId))
            ->exists();
            
        if ($existingTimetable) {
            $roomName = Room::find($data['room_id'])?->name ?? 'Selected room';
            $timeslotDateTime = Timeslot::find($data['timeslot_id'])?->start_time?->format('Y-m-d H:i') ?? 'Selected timeslot';
            $errorMsg = "The combination of {$roomName} and {$timeslotDateTime} is already taken";
            
            // Store error to use with halt()
            $errors['room_id'] = $errorMsg;
            $formHasErrors = true;
            
            // Show notification
            \Filament\Notifications\Notification::make()
                ->title('Schedule Conflict')
                ->body($errorMsg)
                ->danger()
                ->persistent()
                ->send();
        }
        
        // Check if all professors in the jury are available in this timeslot
        $unavailableProfessors = [];
        
        if (!ProfessorService::checkJuryAvailability($timeslot, $project, $timetableId)) {
            // Get the professors for this project
            $professors = $project->professors;
            
            // Find which professors are busy at this timeslot
            foreach ($professors as $professor) {
                if (!ProfessorService::isProfessorAvailable($timeslot, $professor, $timetableId)) {
                    $unavailableProfessors[] = $professor->full_name;
                }
            }
            
            $professorsList = implode(', ', $unavailableProfessors);
            $errorMsg = "Professor(s) not available at this time: {$professorsList}";
            
            // Store error to use with halt()
            $errors['timeslot_id'] = $errorMsg;
            $formHasErrors = true;
            
            // Show notification
            \Filament\Notifications\Notification::make()
                ->title('Professor Availability Conflict')
                ->body($errorMsg)
                ->danger()
                ->persistent()
                ->send();
        }
        
        // Warn if the project's end date is after the timeslot's start time
        if ($project->end_date > $timeslot->start_time) {
            $warningMsg = "The project's end date ({$project->end_date->format('Y-m-d')}) is after the defense date ({$timeslot->start_time->format('Y-m-d')})";
            
            \Filament\Notifications\Notification::make()
                ->title('Warning')
                ->body($warningMsg)
                ->warning()
                ->send();
                
            // This is just a warning, not an error that prevents submission
        }
        
        // If there are errors, return the array to handle in the action
        if ($formHasErrors) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // If everything is valid, return true
        return ['success' => true];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('timeslot_id')
                    ->relationship('timeslot', 'start_time', fn ($query) => $query->active())
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                        // Clear room selection to force user to pick a compatible room
                        $set('room_id', null);
                    })
                    ->helperText('Select a timeslot for the defense')
                    ->required(),
                Forms\Components\Select::make('room_id')
                    ->options(function (callable $get) {
                        $timeslotId = $get('timeslot_id');
                        
                        if (!$timeslotId) {
                            return [];
                        }
                        
                        // Get available rooms for this timeslot
                        $takenRooms = Timetable::where('timeslot_id', $timeslotId)
                            ->pluck('room_id')
                            ->toArray();
                            
                        return Room::available()
                            ->whereNotIn('id', $takenRooms)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->reactive()
                    ->helperText('Available rooms for this timeslot')
                    ->disabled(fn (callable $get) => !$get('timeslot_id'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(false)
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Arrange a schedule')
                    ->mutateFormDataUsing(function (array $data) {
                        // Add user_id if needed
                        $data['user_id'] = auth()->id();
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();
                        return $data;
                    })
                    ->action(function (array $data, Tables\Actions\CreateAction $action) {
                        // Check professors' availability before creating
                        $result = $this->checkProfessorsAvailability($data, $action);
                        
                        if ($result['success']) {
                            // Create timetable
                            $this->getRelationship()->create($data);
                            
                            Notification::make()
                                ->title('Schedule created successfully')
                                ->success()
                                ->send();
                                
                            return;
                        }
                        
                        // Find the first error message to halt with
                        $errorMessage = collect($result['errors'])->values()->first();
                        
                        // Halt execution with error to keep form open
                        $action->halt($errorMessage);
                    }),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->columns([
                Tables\Columns\TextColumn::make('timeslot.start_time')
                    ->dateTime()
                    ->toggleable(false),
                Tables\Columns\TextColumn::make('timeslot.end_time')
                    ->dateTime()
                    ->toggleable(false),
                Tables\Columns\TextColumn::make('room.name')
                    ->toggleable(false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Schedule')
                    ->mutateFormDataUsing(function (array $data) {
                        // Add user_id if needed
                        $data['user_id'] = auth()->id();
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();
                        return $data;
                    })
                    ->action(function (array $data, Tables\Actions\CreateAction $action) {
                        // Check professors' availability before creating
                        $result = $this->checkProfessorsAvailability($data, $action);
                        
                        if ($result['success']) {
                            // Create timetable
                            $this->getRelationship()->create($data);
                            
                            Notification::make()
                                ->title('Schedule created successfully')
                                ->success()
                                ->send();
                                
                            return;
                        }
                        
                        // Find the first error message to halt with
                        $errorMessage = collect($result['errors'])->values()->first();
                        
                        // Halt execution with error to keep form open
                        $action->halt($errorMessage);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(false)
                    ->mutateFormDataUsing(function (array $data) {
                        $data['updated_by'] = auth()->id();
                        return $data;
                    })
                    ->action(function (array $data, Tables\Actions\EditAction $action) {
                        $record = $action->getRecord();
                        
                        // Check professors' availability before updating
                        $result = $this->checkProfessorsAvailability($data, $action, $record->id);
                        
                        if ($result['success']) {
                            // Update timetable
                            $record->update($data);
                            
                            Notification::make()
                                ->title('Schedule updated successfully')
                                ->success()
                                ->send();
                                
                            return;
                        }
                        
                        // Find the first error message to halt with
                        $errorMessage = collect($result['errors'])->values()->first();
                        
                        // Halt execution with error to keep form open
                        $action->halt($errorMessage);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Unschedule')
                    ->icon('heroicon-o-clock'),
            ])
            ->bulkActions([
            ]);
    }
}
