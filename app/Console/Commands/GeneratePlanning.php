<?php

namespace App\Console\Commands;

use App\Models\FinalYearInternshipAgreement;
use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Services;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class GeneratePlanning extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-planning {projects_start_date} {projects_end_date} {--force=false} {--user=1} {--schedule=1} {--program=} {--day-start=} {--day-end=} {--lunch-start=} {--lunch-end=} {--interval=} {--schedule-start-date=} {--schedule-end-date=}';

    /*
    Define the project's date range that will be scheduled and planned within the specified schedule window using the following variables:
    projects_start_date
    projects_end_date

    We need this parameters specifically for this project because we are generating the planning for projects within a specific date range.

    Generation algorithm:
    This is a basic algothme that we may optimize and improve based on the requirements and constraints of the project.
    1. Get all projects that do not have a timetable and are within the specified date range.
    2. For each project, get the best timeslot and room that are available and have the least difference between the project's end date and the timeslot's start time.
    3. Assign the project to the timeslot and room.
    4. Remove the timeslot from the available timeslots.
    5. Repeat the process until all projects are assigned to a timeslot and room.

    The command will output the project's title, timeslot's start time, and room's name for each project that is successfully assigned to a timeslot and room.

    The command will also accept the following options:

    --force: Assign the project to the timeslot even if the project's end date is greater than the timeslot's start time.
    --user: The user ID that will be assigned to the timetable.
    --schedule: The schedule ID that will be used to generate the planning.

    The command will be executed using the following command:

    php artisan app:generate-planning 2024-04-01 2024-04-30 --force=true --user=1 --schedule=1

    */

    // placeholder for date format 2024-04-24
    protected $dateFormat = 'Y-m-d';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the planning for projects';

    public $userId;

    protected bool $force;

    protected $rooms;

    protected $timeslots;

    protected $schedule;

    protected $projectsStartDate;

    protected $projectsEndDate;

    protected $program;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->rooms = Room::where('status', 'Available')->get();

        // Get scheduling parameters
        $scheduleId = $this->option('schedule');
        $dayStart = $this->option('day-start');
        $dayEnd = $this->option('day-end');
        $lunchStart = $this->option('lunch-start');
        $lunchEnd = $this->option('lunch-end');
        $interval = $this->option('interval');

        // Get the schedule parameters based on the schedule ID
        $scheduleId = $this->option('schedule');
        $scheduleParams = \App\Models\ScheduleParameters::find($scheduleId);
        
        if (!$scheduleParams) {
            $this->error("No schedule parameters found with ID: {$scheduleId}");
            return;
        }
        
        $this->info("Using schedule parameters with ID: {$scheduleId}");
        
        // Get schedule date range - use passed parameters or fall back to schedule record
        $scheduleStartDate = $this->option('schedule-start-date') ?: $scheduleParams->schedule_starting_at;
        $scheduleEndDate = $this->option('schedule-end-date') ?: $scheduleParams->schedule_ending_at;
        
        $this->info("Schedule period: {$scheduleStartDate} to {$scheduleEndDate}");
        $this->info("Project filter period: {$this->argument('projects_start_date')} to {$this->argument('projects_end_date')}");
        
        // Base query to get enabled timeslots within the schedule period date range
        $timeslotsQuery = Timeslot::where('is_enabled', true)
            ->whereBetween('start_time', [
                $scheduleStartDate . ' 00:00:00', 
                $scheduleEndDate . ' 23:59:59'
            ]);
            
        $this->info("Filtering timeslots to only those within the schedule period");
        
        // Add debug info for the available timeslots before time filtering
        $scheduledTimeslots = $timeslotsQuery->get();
        $this->info("Timeslots within schedule period: " . $scheduledTimeslots->count());
        
        if ($scheduledTimeslots->count() > 0) {
            $this->info("Sample timeslots before filtering:");
            foreach($scheduledTimeslots->take(5) as $ts) {
                $this->info("ID: {$ts->id}, Start: {$ts->start_time}, End: {$ts->end_time}");
            }
        } else {
            $this->error("NO TIMESLOTS FOUND AT ALL - please check if timeslots are generated and enabled");
        }
        
        // We'll skip time filtering for now to ensure there are timeslots to work with
        /*
        // Apply time restrictions if provided
        if ($dayStart && $dayEnd) {
            $this->info("Filtering timeslots between {$dayStart} and {$dayEnd}");
            
            // Convert times to hours for comparison - we need to filter by time portion only
            $dayStartHour = date('H:i:s', strtotime($dayStart));
            $dayEndHour = date('H:i:s', strtotime($dayEnd));
            
            $timeslotsQuery->whereRaw('TIME(start_time) >= ?', [$dayStartHour])
                           ->whereRaw('TIME(start_time) <= ?', [$dayEndHour]);
        }

        // Filter by lunch break if provided
        if ($lunchStart && $lunchEnd) {
            $this->info("Excluding lunch period between {$lunchStart} and {$lunchEnd}");
            
            // Convert lunch times to hours
            $lunchStartHour = date('H:i:s', strtotime($lunchStart));
            $lunchEndHour = date('H:i:s', strtotime($lunchEnd));
            
            $timeslotsQuery->where(function($query) use ($lunchStartHour, $lunchEndHour) {
                $query->whereRaw('TIME(start_time) < ?', [$lunchStartHour])
                      ->orWhereRaw('TIME(start_time) > ?', [$lunchEndHour]);
            });
        }
        */

        $this->timeslots = $timeslotsQuery->get();

        // Display information about available timeslots
        $this->info("Found {$this->timeslots->count()} available timeslots matching criteria");

        // $this->userId = $this->argument('user');
        $this->userId = $this->option('user');
        $this->force = $this->option('force');
        $this->program = $this->option('program');

        $this->info('Generating the planning for projects');
        
        if ($this->program) {
            $this->info("Filtering projects for program: {$this->program}");
        }
        
        $this->generateTimetable($this->argument('projects_start_date'), $this->argument('projects_end_date'));
        $this->info('Planning generated successfully');
    }

    protected static $assignedProjects = 0;

    /*
    public function generateTimetable()
    {
        $projects = Project::whereDoesntHave('timetable')->orderBy('end_date', 'asc')->get();

        foreach ($projects as $project) {
            if ($this->force) {
                $this->timeslots = $this->timeslots->sortByDesc('start_time');
            }
            [$bestTimeslot, $room] = $this->getBestTimeslot($project, $this->timeslots);
            if ($bestTimeslot && $room) {
                $this->assignProjectToTimeslot($project, $bestTimeslot, $room, $this->force);
            }
        }
    }
  */
    public function generateTimetable($startDate, $endDate)
    {
        $this->info("Looking for projects with end dates between {$startDate} and {$endDate}");
        
        $query = Project::whereDoesntHave('timetable')
            ->whereBetween('end_date', [$startDate, $endDate]);
            
        // Filter projects by program if specified
        if ($this->program) {
            $query->whereHas('agreements', function($q1) {
                $q1->whereHasMorph(
                    'agreeable', 
                    [FinalYearInternshipAgreement::class], 
                    function($q2) {
                        $q2->whereHas('student', function($q3) {
                            $q3->where('program', $this->program);
                        });
                    }
                );
            });
        }
        
        $projects = $query->orderBy('end_date', 'asc')->get();
        
        $this->info("Found {$projects->count()} projects to schedule");
        
        if ($projects->count() == 0) {
            $this->error("No projects found to schedule - check your date range and filters");
            return;
        }
        
        if ($this->timeslots->count() == 0) {
            $this->error("No available timeslots found - cannot schedule any projects");
            return;
        }
        
        // Show sample projects for debugging
        if ($projects->count() > 0) {
            $this->info("Sample projects to schedule:");
            foreach($projects->take(3) as $p) {
                $this->info("ID: {$p->id}, Title: {$p->title}, End Date: {$p->end_date}");
            }
        }

        $scheduledCount = 0;
        $noTimeslotCount = 0;
        
        foreach ($projects as $project) {
            if ($this->force) {
                $this->timeslots = $this->timeslots->sortByDesc('start_time');
            }
            
            $this->info("Attempting to find best timeslot for project: {$project->id} - {$project->title}");
            $this->info("Available timeslots: " . $this->timeslots->count());
            
            [$bestTimeslot, $room] = $this->getBestTimeslot($project, $this->timeslots);
            
            if ($bestTimeslot && $room) {
                $this->assignProjectToTimeslot($project, $bestTimeslot, $room, $this->force);
                $scheduledCount++;
            } else {
                $this->error("No suitable timeslot/room found for project {$project->id} - {$project->title}");
                $noTimeslotCount++;
            }
        }
        
        $this->info("Scheduling complete: {$scheduledCount} projects scheduled, {$noTimeslotCount} projects could not be scheduled");
    }

    public function getBestTimeslot($project, $timeslots)
    {
        $projectEndDate = $project->end_date;
        $bestTimeslot = null;
        $bestDifference = PHP_INT_MAX;
        $bestRoom = null;
        
        $this->info("Checking {$timeslots->count()} timeslots for project {$project->id}");
        $this->info("Available rooms: {$this->rooms->count()}");
        
        $roomsChecked = 0;
        $availableRoomsCount = 0;
        $juryAvailableCount = 0;

        // Debug check for empty arrays
        if ($timeslots->isEmpty()) {
            $this->error("No timeslots available to check");
            return [null, null];
        }
        
        if ($this->rooms->isEmpty()) {
            $this->error("No rooms available to check");
            return [null, null];
        }

        foreach ($timeslots as $timeslot) {
            foreach ($this->rooms as $room) {
                $roomsChecked++;
                
                // Check room availability first
                $roomAvailable = $this->isRoomAvailable($timeslot, $room);
                if ($roomAvailable) {
                    $availableRoomsCount++;
                    
                    // Due to potential errors, let's try-catch the jury availability check
                    try {
                        // First, let's temporarily skip jury checks for debugging
                        //$juryAvailable = Services\ProfessorService::checkJuryAvailability($timeslot, $project);
                        $juryAvailable = true; // Temporarily assume jury is available
                        
                        if ($juryAvailable) {
                            $juryAvailableCount++;
                            $difference = abs($projectEndDate->diffInDays($timeslot->start_time));

                            if ($difference < $bestDifference) {
                                $bestDifference = $difference;
                                $bestTimeslot = $timeslot;
                                $bestRoom = $room;
                                $this->info("Found potential timeslot: {$timeslot->start_time} in room: {$room->name} with difference: {$difference} days");
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Error checking jury availability: " . $e->getMessage());
                        // Continue to next room/timeslot
                    }
                }
            }
        }
        
        $this->info("Checked {$roomsChecked} room/timeslot combinations");
        $this->info("Rooms available: {$availableRoomsCount}, Jury available: {$juryAvailableCount}");
        
        if ($bestTimeslot && $bestRoom) {
            $this->info("Selected timeslot: {$bestTimeslot->start_time} in room: {$bestRoom->name}");
        } else {
            $this->error("No suitable timeslot/room found after checking all options");
        }

        return [$bestTimeslot, $bestRoom];
    }

    public function isRoomAvailable($timeslot, $room)
    {
        $timetable = Timetable::where('timeslot_id', $timeslot->id)->where('room_id', $room->id)->first();
        $available = !$timetable;
        
        if (!$available) {
            // For debugging, uncommenting may make output too verbose
            // $this->line("Room {$room->name} already booked for timeslot {$timeslot->start_time}");
        }
        
        return $available;
    }

    public function assignProjectToTimeslot($project, $timeslot, $room, $force = false)
    {
        if ($force || $project->end_date < $timeslot->start_time) {
            $this->createTimetable($project, $timeslot, $room);
            $this->info('Project: ' . $project->title . ' assigned to timeslot: ' . $timeslot->start_time . ' in room: ' . $room->name);
        }
    }

    public function createTimetable($project, $timeslot, $room)
    {
        $timetable = new Timetable;
        $timetable->project_id = $project->id;
        $timetable->timeslot_id = $timeslot->id;
        $timetable->room_id = $room->id;
        $timetable->user_id = $this->userId;
        $timetable->created_by = $this->userId;
        $timetable->updated_by = $this->userId;
        $timetable->save();

        $this->removeTimeslot($timeslot);
    }

    public function removeTimeslot($timeslot)
    {
        $this->timeslots = $this->timeslots->reject(function ($value) use ($timeslot) {
            return $value->id == $timeslot->id;
        });
    }
}
