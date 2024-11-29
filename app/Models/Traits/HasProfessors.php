<?php

namespace App\Models\Traits;

use App\Models\Professor;
use App\Models\ProfessorProject;

trait HasProfessors
{
    public function professors()
    {
        return $this->morphToMany(Professor::class, 'project', 'professor_projects', 'project_id', 'professor_id')
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
}
