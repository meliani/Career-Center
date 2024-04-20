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
    protected $signature = 'app:generate-planning {user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the planning for projects';

    public $userId = null;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->userId = $this->argument('user');
        $this->info('Generating the planning for projects');
        $this->generateTimetable();
        $this->info('Planning generated successfully');
    }

    protected static $assignedProjects = 0;

    public function generateTimetable()
    {
        $projects = Project::whereDoesntHave('timetable')->orderBy('end_date', 'asc')->get();
        $timeslots = Timeslot::get();
        $rooms = Room::get();
        foreach ($projects as $project) {
            $isProjectAssigned = false;
            foreach ($timeslots as $timeslot) {
                $isTimeslotAssigned = false;
                foreach ($rooms as $room) {
                    $isProjectAssigned = Timetable::where('project_id', $project->id)
                        ->exists();

                    if ($isProjectAssigned) {
                        $this->info('Project: ' . $project->title . ' is already assigned');

                        continue;
                    }
                    // Check if the timeslot and room are already assigned in the timetable
                    $isTimeslotAssigned = Timetable::where('timeslot_id', $timeslot->id)
                        ->where('room_id', $room->id)
                        ->exists();
                    if ($isTimeslotAssigned || $isProjectAssigned) {
                        $this->info('Timeslot: ' . $timeslot->start_time . ' in Room: ' . $room->name . ' is already assigned');

                        continue;
                    }

                    // check if the given timeslot is superior than end_date of the project
                    if ($project->end_date->lessThan($timeslot->end_time)) {
                        // $this->info($project->end_date->lessThan($timeslot->end_time));
                        $this->info('Project: ' . $project->id . ' with end date: ' . $project->end_date . ' is inferior to ' . $timeslot->start_time . ' in Room: ' . $room->name);
                        if (
                            Services\ProfessorService::checkJuryAvailability($timeslot, $room, $project)
                            && Services\RoomService::checkRoomAvailability($timeslot, $room)
                            && Services\TimeslotService::checkTimeslotAvailability($timeslot, $room)
                        ) {
                            $this->info('Project: ' . $project->id . ' assigned to Timeslot: ' . $timeslot->start_time . ' - ' . $timeslot->end_time . ' in Room: ' . $room->name);
                            // Assign the project to the timeslot and room in the timetable
                            Timetable::create([
                                'timeslot_id' => $timeslot->id,
                                'room_id' => $room->id,
                                'user_id' => $this->userId,
                                'project_id' => $project->id,
                                'created_by' => $this->userId,
                                'updated_by' => $this->userId,
                            ]);

                            // Decrement the remaining slots for the timeslot
                            $timeslot->decrement('remaining_slots');

                            self::$assignedProjects++;
                        }

                        continue;
                    }
                    // Check if the timeslot, room, and project meet the necessary conditions

                }
            }
        }

        return self::$assignedProjects;
    }
}
