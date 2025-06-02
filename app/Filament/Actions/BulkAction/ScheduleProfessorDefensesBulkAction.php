<?php

namespace App\Filament\Actions\BulkAction;

use App\Models\Professor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Models\Year;
use Carbon\Carbon;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class ScheduleProfessorDefensesBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'schedule-professor-defenses';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Schedule Selected Projects')
            ->icon('heroicon-o-calendar')
            ->modalHeading('Schedule Selected Projects')
            ->modalDescription('Bulk schedule defenses for the selected projects. You can specify the date range, professor constraints, and other parameters.')
            ->modalSubmitActionLabel('Schedule Selected Projects')
            ->deselectRecordsAfterCompletion();

        $this->form([
            Forms\Components\Select::make('professor_id')
                ->label('Professor')
                ->options(function() {
                    return Professor::query()
                        ->orderBy('last_name')
                        ->orderBy('first_name')
                        ->get()
                        ->pluck('full_name', 'id');
                })
                ->required()
                ->searchable()
                ->helperText('Select the professor to schedule defenses for'),
                
            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->default(now())
                ->required()
                ->helperText('The date to start scheduling from'),
                
            Forms\Components\DatePicker::make('end_date')
                ->label('End Date')
                ->default(now()->addDays(7))
                ->required()
                ->after('start_date')
                ->helperText('The date to end scheduling at'),
                
            Forms\Components\TagsInput::make('exclude_dates')
                ->label('Exclude Dates')
                ->helperText('Enter dates to exclude from scheduling (format: YYYY-MM-DD)')
                ->placeholder('YYYY-MM-DD'),
                
            Forms\Components\Toggle::make('check_professor_availability')
                ->label('Check Professor Availability')
                ->default(true)
                ->helperText('If enabled, will only schedule when the professor has no other defenses at the same time'),
                
            Forms\Components\Radio::make('professor_role_filter')
                ->label('Professor Role Filter')
                ->options([
                    'any' => 'Any role (supervisor or reviewer)',
                    'supervisor' => 'Only as supervisor',
                    'reviewer' => 'Only as reviewer',
                ])
                ->default('any')
                ->required()
                ->inline(),
                
            Forms\Components\Select::make('program_filter')
                ->label('Program Filter')
                ->options(\App\Enums\Program::class)
                ->placeholder('All Programs')
                ->helperText('Optionally filter projects by specific program'),
        ]);

        $this->action(function (Collection $records, array $data): void {
            // Process and schedule the selected projects
            $this->scheduleSelectedProjects($records, $data);
        });
    }

    protected function scheduleSelectedProjects(Collection $records, array $data): void
    {
        // Extract parameters
        $professor = Professor::find($data['professor_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $professorRoleFilter = $data['professor_role_filter'];
        $checkAvailability = $data['check_professor_availability'];
        
        // Parse excluded dates
        $excludeDates = [];
        if (isset($data['exclude_dates']) && is_array($data['exclude_dates'])) {
            foreach ($data['exclude_dates'] as $date) {
                $excludeDates[] = Carbon::parse(trim($date))->format('Y-m-d');
            }
        }
        
        // Get available timeslots
        $availableTimeslots = $this->getAvailableTimeslots($startDate, $endDate, $excludeDates);
        
        if ($availableTimeslots->isEmpty()) {
            Notification::make()
                ->title('No Available Timeslots')
                ->body('No available timeslots found in the specified date range.')
                ->danger()
                ->send();
            return;
        }
        
        // Filter records by professor role if needed
        if ($professorRoleFilter !== 'any') {
            $records = $this->filterProjectsByProfessorRole($records, $professor, $professorRoleFilter);
        }
        
        // Filter records by program if specified
        if (!empty($data['program_filter'])) {
            $program = $data['program_filter'];
            $records = $records->filter(function ($project) use ($program) {
                return $project->agreements()->whereHas('agreeable', function ($query) use ($program) {
                    $query->whereHas('student', function ($query) use ($program) {
                        $query->where('program', $program);
                    });
                })->exists();
            });
        }
        
        // Filter out already scheduled projects
        $projects = $records->filter(function ($project) {
            return !$project->timetable()->exists();
        });
        
        if ($projects->isEmpty()) {
            Notification::make()
                ->title('No Eligible Projects')
                ->body('No unscheduled projects found that match the criteria.')
                ->warning()
                ->send();
            return;
        }
        
        // Schedule the projects
        $scheduledCount = 0;
        $scheduledByDate = []; // Keep track of how many defenses scheduled per day
        $maxDefensesPerDay = 3; // Default max per day
        
        foreach ($projects as $project) {
            foreach ($availableTimeslots as $key => $timeslot) {
                $date = $timeslot->start_time->format('Y-m-d');
                
                // Check if we've reached the max defenses per day for this professor
                if (isset($scheduledByDate[$date]) && $scheduledByDate[$date] >= $maxDefensesPerDay) {
                    continue;
                }
                
                // Check professor availability if required
                if ($checkAvailability && !$this->isProfessorAvailable($professor, $timeslot)) {
                    continue;
                }
                
                // Find available rooms for this timeslot
                $availableRooms = $this->findAvailableRoom($timeslot);
                
                if ($availableRooms->isEmpty()) {
                    // No rooms available for this timeslot, try next timeslot
                    continue;
                }
                
                // Try to schedule in any of the available rooms
                $scheduled = false;
                
                foreach ($availableRooms as $room) {
                    try {
                        // Schedule the defense
                        $timetable = new Timetable();
                        $timetable->timeslot_id = $timeslot->id;
                        $timetable->room_id = $room->id;
                        $timetable->project_id = $project->id;
                        // $timetable->user_id = auth()->id();
                        // $timetable->created_by = auth()->id();
                        // $timetable->updated_by = auth()->id();
                        $timetable->scheduled_by = auth()->id();
                        $timetable->save();
                        
                        // Increment counters
                        $scheduledCount++;
                        $scheduledByDate[$date] = ($scheduledByDate[$date] ?? 0) + 1;
                        
                        // Remove used timeslot
                        $availableTimeslots->forget($key);
                        
                        $scheduled = true;
                        break; // Successfully scheduled, break out of room loop
                    } catch (\Exception $e) {
                        // Log error but try next room
                        report($e);
                        continue;
                    }
                }
                
                if ($scheduled) {
                    break; // Successfully scheduled, move to next project
                }
            }
        }
        
        // Show notification with results
        if ($scheduledCount > 0) {
            Notification::make()
                ->title('Projects Scheduled')
                ->body("Successfully scheduled {$scheduledCount} out of {$projects->count()} projects for professor {$professor->full_name}.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('No Projects Scheduled')
                ->body("Could not schedule any projects. Please check professor availability and timeslots.")
                ->warning()
                ->send();
        }
    }

    protected function getAvailableTimeslots($startDate, $endDate, $excludeDates)
    {
        return Timeslot::where('is_enabled', true)
            ->where('start_time', '>=', $startDate)
            ->where('start_time', '<=', $endDate)
            ->whereNotIn(\DB::raw('DATE(start_time)'), $excludeDates)
            ->whereDoesntHave('timetable')
            ->orderBy('start_time')
            ->get();
    }

    protected function isProfessorAvailable(Professor $professor, Timeslot $timeslot)
    {
        // Get projects for this professor
        $professorProjects = $professor->projects->pluck('id')->toArray();
        
        // Check if any of the professor's projects have a timetable entry for this timeslot
        $existingSchedule = Timetable::where('timeslot_id', $timeslot->id)
            ->whereIn('project_id', $professorProjects)
            ->exists();
            
        return !$existingSchedule;
    }

    protected function findAvailableRoom(Timeslot $timeslot)
    {
        // Get rooms already used for this timeslot
        $usedRoomIds = Timetable::where('timeslot_id', $timeslot->id)
            ->pluck('room_id')
            ->toArray();
            
        // Find all available rooms - using the 'status' column and Available enum value
        return Room::where('status', \App\Enums\RoomStatus::Available)
            ->whereNotIn('id', $usedRoomIds)
            ->get();
    }

    protected function filterProjectsByProfessorRole(Collection $projects, Professor $professor, $roleFilter)
    {
        return $projects->filter(function ($project) use ($professor, $roleFilter) {
            $professorProject = $project->professors()->where('professor_id', $professor->id)->first();
            
            if (!$professorProject) {
                return false;
            }
            
            if ($roleFilter === 'supervisor') {
                return $professorProject->pivot->jury_role === \App\Enums\JuryRole::Supervisor->value;
            } elseif ($roleFilter === 'reviewer') {
                return in_array($professorProject->pivot->jury_role, [
                    \App\Enums\JuryRole::FirstReviewer->value, 
                    \App\Enums\JuryRole::SecondReviewer->value
                ]);
            }
            
            return true;
        });
    }
}
