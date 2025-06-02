<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Services;

class TimetableService
{
    protected static $assignedProjects = 0;

    public static function generateTimetable()
    {
        $projects = Project::whereDoesntHave('timetable')->orderBy('end_date', 'asc')->get();
        $timeslots = Timeslot::get();
        $rooms = Room::get();

        // dd($projects, $timeslots, $rooms);
        foreach ($projects as $project) {
            $isProjectAssigned = false;
            // if project is already assigned to a timeslot

            foreach ($timeslots as $timeslot) {
                $isTimeslotAssigned = false;
                foreach ($rooms as $room) {
                    $isProjectAssigned = Timetable::where('project_id', $project->id)
                        ->exists();

                    if ($isProjectAssigned) {
                        continue;
                    }
                    // Check if the timeslot and room are already assigned in the timetable
                    $isTimeslotAssigned = Timetable::where('timeslot_id', $timeslot->id)
                        ->where('room_id', $room->id)
                        ->exists();
                    if ($isTimeslotAssigned || $isProjectAssigned) {
                        continue;
                    }

                    // check if the given timeslot is superior than end_date of the project
                    if ($project->end_date->greaterThan($timeslot->end_time)) {
                        continue;
                    }
                    // Check if the timeslot, room, and project meet the necessary conditions
                    if (Services\ProfessorService::checkJuryAvailability($timeslot, $room, $project)
                        && Services\RoomService::checkRoomAvailability($timeslot, $room)
                        && Services\TimeslotService::checkTimeslotAvailability($timeslot, $room)) {

                        // Assign the project to the timeslot and room in the timetable
                        Timetable::create([
                            'timeslot_id' => $timeslot->id,
                            'room_id' => $room->id,
                            // 'user_id' => auth()->id(),
                            'project_id' => $project->id,
                            // 'created_by' => auth()->id(),
                            // 'updated_by' => auth()->id(),
                            'scheduled_by' => auth()->id(),
                        ]);

                        // Decrement the remaining slots for the timeslot
                        $timeslot->decrement('remaining_slots');

                        self::$assignedProjects++;
                    }
                }
            }
        }

        return self::$assignedProjects;
    }
}
