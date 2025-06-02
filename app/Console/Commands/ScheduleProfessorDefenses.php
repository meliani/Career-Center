<?php

namespace App\Console\Commands;

use App\Models\Professor;
use App\Models\Project;
use App\Models\Timeslot;
use App\Models\Year;
use App\Models\Room;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ScheduleProfessorDefenses extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-professor-defenses 
                            {--professor-id= : The ID of the professor to schedule}
                            {--start-date= : Start date for scheduling (format: Y-m-d)}
                            {--end-date= : End date for scheduling (format: Y-m-d)}
                            {--exclude-dates= : Comma-separated list of dates to exclude (format: Y-m-d)}
                            {--max-defenses-per-day=3 : Maximum number of defenses per day}
                            {--program= : Filter projects by specific program (AMOA, ASEDS, DATA, etc.)}
                            {--year-id= : Academic year ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule defenses for a specific professor in a given time range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get parameters
        $professorId = $this->option('professor-id');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $excludeDatesStr = $this->option('exclude-dates');
        $maxDefensesPerDay = $this->option('max-defenses-per-day');
        $program = $this->option('program');
        $yearId = $this->option('year-id') ?: Year::current()->id;

        // Validate professor
        $professor = Professor::find($professorId);
        if (!$professor) {
            $this->error("Professor with ID {$professorId} not found.");
            return 1;
        }

        // Parse dates
        try {
            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::today();
            $endDate = $endDate ? Carbon::parse($endDate) : $startDate->copy()->addDays(7);
            
            // Parse excluded dates
            $excludeDates = [];
            if ($excludeDatesStr) {
                $excludeDatesList = explode(',', $excludeDatesStr);
                foreach ($excludeDatesList as $date) {
                    $excludeDates[] = Carbon::parse(trim($date))->format('Y-m-d');
                }
            }
        } catch (\Exception $e) {
            $this->error("Date parsing error: " . $e->getMessage());
            return 1;
        }

        // Get the professor's projects that need scheduling
        $projects = $this->getProjectsForProfessor($professor);
        if ($projects->isEmpty()) {
            $this->info("No projects found for professor {$professor->full_name} that need scheduling.");
            return 0;
        }
        
        $this->info("Found {$projects->count()} projects for professor {$professor->full_name} that need scheduling.");
        
        // Get available timeslots in the date range
        $availableTimeslots = $this->getAvailableTimeslots($startDate, $endDate, $excludeDates, $yearId);
        if ($availableTimeslots->isEmpty()) {
            $this->error("No available timeslots found in the specified date range.");
            return 1;
        }
        
        $this->info("Found {$availableTimeslots->count()} available timeslots.");
        
        // Schedule defenses
        $scheduledCount = $this->scheduleDefenses($projects, $availableTimeslots, $professor, $maxDefensesPerDay);
        
        $this->info("Successfully scheduled {$scheduledCount} defenses for professor {$professor->full_name}.");
        return 0;
    }

    /**
     * Get projects for the professor that need scheduling
     */
    private function getProjectsForProfessor(Professor $professor)
    {
        $program = $this->option('program');
        
        $query = Project::whereHas('professors', function ($query) use ($professor) {
            $query->where('professor_id', $professor->id);
        })->whereDoesntHave('timetable');
        
        // Add program filter if specified
        if ($program) {
            $query->whereHas('agreements', function ($query) use ($program) {
                $query->whereHas('agreeable', function ($query) use ($program) {
                    $query->whereHas('student', function ($query) use ($program) {
                        $query->where('program', $program);
                    });
                });
            });
        }
        
        return $query->get();
    }

    /**
     * Get available timeslots in the date range
     */
    private function getAvailableTimeslots($startDate, $endDate, $excludeDates, $yearId)
    {
        return Timeslot::where('year_id', $yearId)
            ->where('is_enabled', true)
            ->where('start_time', '>=', $startDate)
            ->where('start_time', '<=', $endDate)
            ->whereNotIn(
                \DB::raw('DATE(start_time)'), 
                $excludeDates
            )
            ->whereDoesntHave('timetable')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Schedule defenses for projects
     */
    private function scheduleDefenses($projects, $availableTimeslots, $professor, $maxDefensesPerDay)
    {
        $scheduledCount = 0;
        $scheduledByDate = []; // Keep track of how many defenses scheduled per day
        
        foreach ($projects as $project) {
            foreach ($availableTimeslots as $key => $timeslot) {
                $date = $timeslot->start_time->format('Y-m-d');
                
                // Check if we've reached the max defenses per day for this professor
                if (isset($scheduledByDate[$date]) && $scheduledByDate[$date] >= $maxDefensesPerDay) {
                    continue;
                }
                
                // Check if this timeslot conflicts with another defense for this professor
                if ($this->isProfessorAvailable($professor, $timeslot)) {
                    // Find an available room
                    $room = $this->findAvailableRoom($timeslot);
                    if (!$room) {
                        continue; // No room available, try next timeslot
                    }
                    
                    // Schedule the defense
                    $timetable = new Timetable();
                    $timetable->timeslot_id = $timeslot->id;
                    $timetable->room_id = $room->id;
                    $timetable->project_id = $project->id;
                    $timetable->year_id = $timeslot->year_id;
                    $timetable->created_by = 1; // System user
                    // $timetable->updated_by = 1;
                    $timetable->save();
                    
                    $this->info("Scheduled defense for project ID {$project->id} at {$timeslot->start_time} in room {$room->name}");
                    
                    // Increment counters
                    $scheduledCount++;
                    $scheduledByDate[$date] = ($scheduledByDate[$date] ?? 0) + 1;
                    
                    // Remove used timeslot
                    $availableTimeslots->forget($key);
                    break;
                }
            }
        }
        
        return $scheduledCount;
    }

    /**
     * Check if the professor is available at this timeslot
     */
    private function isProfessorAvailable(Professor $professor, Timeslot $timeslot)
    {
        // Get projects for this professor
        $professorProjects = $professor->projects->pluck('id')->toArray();
        
        // Check if any of the professor's projects have a timetable entry for this timeslot
        $existingSchedule = Timetable::where('timeslot_id', $timeslot->id)
            ->whereIn('project_id', $professorProjects)
            ->exists();
            
        return !$existingSchedule;
    }

    /**
     * Find an available room for this timeslot
     */
    private function findAvailableRoom(Timeslot $timeslot)
    {
        // Get rooms already used for this timeslot
        $usedRoomIds = Timetable::where('timeslot_id', $timeslot->id)
            ->pluck('room_id')
            ->toArray();
            
        // Find an available room
        return Room::where('is_enabled', true)
            ->whereNotIn('id', $usedRoomIds)
            ->first();
    }
}
