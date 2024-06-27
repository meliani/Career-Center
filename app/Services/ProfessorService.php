<?php

namespace App\Services;

use App\Models\Professor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;

class ProfessorService
{
    protected static Timeslot $timeslot;

    protected static Room $room;

    protected static Project $project;

    public static $timetableId;

    public static function checkJuryAvailability(Timeslot $timeslot, Project $project, ?int $timetableId = null)
    {
        self::$timeslot = $timeslot;
        self::$project = $project;
        self::$timetableId = $timetableId;

        $jury = $project->professors;
        // we will check every professor in this jury if he is available in this timeslot
        foreach ($jury as $professor) {
            // we will check if this professor is available in this timeslot
            if (! self::isProfessorAvailable(self::$timeslot, $professor, self::$timetableId)) {
                return false;
            }

        }

        return true;

    }

    public static function getUnavailableJury(Timeslot $timeslot, Project $project, ?int $timetableId = null)
    {
        self::$timeslot = $timeslot;
        self::$project = $project;
        self::$timetableId = $timetableId;

        $jury = $project->professors;
        // we will check every professor in this jury if he is available in this timeslot
        foreach ($jury as $professor) {
            // we will check if this professor is available in this timeslot
            if (! self::isProfessorAvailable(self::$timeslot, $professor, self::$timetableId)) {
                return $professor;
            }

        }

        return null;
    }

    public static function isProfessorAvailable(Timeslot $timeslot, Professor $professor, ?int $timetableId = null)
    {
        //  timetable s related to this timeslot and the project, the projects are related with the professor (hasmany)
        // get professors projects except the current project
        $projects = $professor->projects->where('id', '!=', self::$project->id);
        $timetables = Timetable::where('timeslot_id', $timeslot->id)
            // ->Where('id', '!=', $timetableId)
            ->whereIn('project_id', $projects->pluck('id'))
            ->get();

        return $timetables->count() === 0;
    }

    public static function checkJuryConditions(Timeslot $timeslot, Room $room, Project $project)
    {
        // we will check if any professor from this jury has been scheduled more than 3 times during this day
        $jury = $project->professors;
        foreach ($jury as $professor) {
            if (! self::checkProfessorConditions(self::$timeslot, $professor)) {
                return false;
            }
        }

    }

    public static function checkProfessorConditions(Timeslot $timeslot, Professor $professor)
    {
        // we will get timetable for the current day and the current professor
        // we can get current day from timeslot and the professor from the project
        $projects = $professor->projects;
        $timetables = Timetable::where('timeslot_id', $timeslot->id)
            ->whereIn('project_id', $projects->pluck('id'))
            ->get();
        // if the professor has been scheduled more than 3 times during this day
        if ($timetables->count() > 3) {
            return false;
        }

        return true;

    }
}
