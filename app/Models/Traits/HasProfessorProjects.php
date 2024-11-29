<?php

namespace App\Models\Traits;

use App\Enums\JuryRole;
use App\Models\Timetable;

trait HasProfessorProjects
{
    public function timetable()
    {
        return $this->morphOne(Timetable::class, 'schedulable');
    }

    public function timetables()
    {
        return $this->morphMany(Timetable::class, 'schedulable');
    }

    public function supervisor()
    {
        return $this->professors()
            ->wherePivot('jury_role', JuryRole::Supervisor->value);
    }

    public function reviewers()
    {
        return $this->professors()
            ->wherePivot('jury_role', JuryRole::Reviewer->value)
            ->orWherePivot('jury_role', JuryRole::Reviewer1->value)
            ->orWherePivot('jury_role', JuryRole::Reviewer2->value);
    }

    public function markAllProfessorsAsPresent()
    {
        $this->professors()->each(function ($professor) {
            $this->professors()->updateExistingPivot($professor->id, ['was_present' => true]);
        });
    }
}
