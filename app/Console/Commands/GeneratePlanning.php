<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Services;
use Illuminate\Console\Command;

class GeneratePlanning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-planning {--force=false} {--user=1}';

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
        $this->generateTimetable();
        $this->info('Planning generated successfully');
    }

    protected static $assignedProjects = 0;

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

    public function getBestTimeslot($project, $timeslots)
    {
        $projectEndDate = $project->end_date; // Assuming 'end_date' is the field name for the project's end date
        $bestTimeslot = null;
        $bestDifference = PHP_INT_MAX;
        $bestRoom = null;

        foreach ($timeslots as $timeslot) {
            foreach ($this->rooms as $room) {
                if ($this->isRoomAvailable($timeslot, $room) && Services\ProfessorService::checkJuryAvailability($timeslot, $room, $project)) {
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
        $timetable = new Timetable();
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
