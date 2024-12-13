<?php

namespace App\Console\Commands;

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
    protected $signature = 'app:generate-planning {projects_start_date} {projects_end_date} {--force=false} {--user=1} {--schedule=1}';

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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->rooms = Room::where('status', 'Available')->get();

        $this->timeslots = Timeslot::where('is_enabled', true)->get();

        // $this->userId = $this->argument('user');
        $this->userId = $this->option('user');
        $this->force = $this->option('force');
        $this->info('Generating the planning for projects');
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
        $projects = Project::whereDoesntHave('timetable')
            ->whereBetween('end_date', [$startDate, $endDate])
            ->orderBy('end_date', 'asc')
            ->get();

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

    public function getBestTimeslot($project, $timeslots)
    {
        $projectEndDate = $project->end_date;
        $bestTimeslot = null;
        $bestDifference = PHP_INT_MAX;
        $bestRoom = null;

        foreach ($timeslots as $timeslot) {
            foreach ($this->rooms as $room) {
                if ($this->isRoomAvailable($timeslot, $room) && Services\ProfessorService::checkJuryAvailability($timeslot, $project)) {
                    $difference = abs($projectEndDate->diffInDays($timeslot->start_time));

                    if ($difference < $bestDifference) {
                        $bestDifference = $difference;
                        $bestTimeslot = $timeslot;
                        $bestRoom = $room;
                    }
                }
            }
        }

        return [$bestTimeslot, $bestRoom];
    }

    public function isRoomAvailable($timeslot, $room)
    {
        $timetable = Timetable::where('timeslot_id', $timeslot->id)->where('room_id', $room->id)->first();

        return ! $timetable;
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
