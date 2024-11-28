<?php

namespace App\Models\Traits;

use App\Enums\JuryRole;
use App\Models\Professor;
use App\Models\ProfessorProject;
use App\Models\Student;
use App\Models\Timetable;

trait Projectable
{
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function professors()
    {
        return $this->morphToMany(Professor::class, 'project', 'professor_projects')
            ->withPivot(
                'jury_role',
                'votes',
                'is_president',
                'was_present',
                'created_by',
                'updated_by',
                'approved_by',
                'supervision_status',
                'last_meeting_date',
                'next_meeting_date',
            )
            ->using(ProfessorProject::class)
            ->withTimestamps();
    }

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

    public function hasTeammate()
    {
        return $this->students()->count() > 1;
    }

    public function markAllProfessorsAsPresent()
    {
        $this->professors()->each(function ($professor) {
            $this->professors()->updateExistingPivot($professor->id, ['was_present' => true]);
        });
    }
}
