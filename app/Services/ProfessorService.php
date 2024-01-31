<?php

namespace App\Services;

use App\Models\Jury;

class JuryService
{
    public function getJuries($professor_ids, $timeslot_id)
    {
        $professors = Jury::whereHas('professor', function ($query) use ($professor_ids) {
            $query->whereIn('id', $professor_ids);
        })->whereDoesntHave('timetables', function ($query) use ($timeslot_id) {
            $query->where('timeslot_id', $timeslot_id);
        })->get();
        return $professors;
    }
}