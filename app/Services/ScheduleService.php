<?php

namespace App\Services;

use App\Models\DefenseSchedule;
use App\Models\Internship;
use App\Models\Project;

class ScheduleService
{
    public static function AssignInternshipsToProjects($record){
        $signedInternships = Internship::where('status', '=', 'Signed')->get();
        // assign signed internships to new projects
        foreach ($signedInternships as $signedInternship) {
            $project = Project::create([
                'title' => $signedInternship->title,
                'description' => $signedInternship->description,
                'updated_at' => $signedInternship->updated_at,
            ]);
            $signedInternship->project_id = $project->id;
            $signedInternship->save();
        }    }
    public static function createDefenseScheduleWithHeadAndInternships($headOfJury, $internships, $room)
    {
        // Create a new DefenseSchedule
        $defenseSchedule = new DefenseSchedule;
        $defenseSchedule->headOfJury()->associate($headOfJury);
        $defenseSchedule->room = $room;
        $defenseSchedule->save();

        // Assign internships to the DefenseSchedule
        foreach ($internships as $internship) {
            $defenseSchedule->internships()->attach($internship->id);
        }

        return $defenseSchedule;
    }
    public static function calculateDefenseTimeSlot($minutes_per_slot, $working_from, $working_to)
    {
        $timeSlots = ($working_to - $working_from) / $minutes_per_slot;
        $defenseTimeSlot = rand(0, $timeSlots);
        return $defenseTimeSlot;
    }
    public static function isTimeSlotAvailable($defenseTimeSlot, $starting_from, $ending_at)
    {
        return $defenseTimeSlot >= $starting_from && $defenseTimeSlot <= $ending_at;
    }
    public static function ScheduleHeadOfJury()
    {
        $parameters = ScheduleParameters::first();
        $professors = Professor::all();
        $projects = Project::all();


        /*         foreach ($projects as $project) {
            $project->professors()->attach($professors->random()->id, ['role' => 'HeadOfJury']);
            $project->save();
        } */

        $professorDefenses = [];
        $roomsUsed = 0;

        foreach ($projects as $project) {
            foreach ($professors as $professor) {
                // Check if the professor has reached the max defenses limit
                if (isset($professorDefenses[$professor->id]) && $professorDefenses[$professor->id] >= $max_defenses_per_professor) {
                    continue;
                }

                // Calculate the defense time slot
                $defenseTimeSlot = calculateDefenseTimeSlot($minutes_per_slot, $working_from, $working_to);

                // Check if the defense time slot is within the schedule thresholds and the room is available
                if (isTimeSlotAvailable($defenseTimeSlot, $starting_from, $ending_at) && $roomsUsed < $max_rooms) {
                    // Assign the professor to the project
                    $project->professors()->attach($professor->id, ['role' => 'HeadOfJury']);
                    $project->save();

                    // Update the number of defenses for the professor
                    if (!isset($professorDefenses[$professor->id])) {
                        $professorDefenses[$professor->id] = 0;
                    }
                    $professorDefenses[$professor->id]++;

                    // Update the number of rooms used
                    $roomsUsed++;

                    // Break the loop as the professor has been assigned to the project
                    break;
                }
            }
        }

        // we distribute professors randomy on the defenses schedule, 
        // adding it first to a project_professor pivot table
        // then we get the project_id and the professor_id and add it to the defenses table
        // we get the project_id and the professor_id and add it to the defense_schedule table

    }

}