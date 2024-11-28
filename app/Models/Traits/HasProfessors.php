<?php

namespace App\Models\Traits;

trait HasProfessors
{
    // ...existing code...

    public function professors()
    {
        return $this->morphToMany(Professor::class, 'professor_projectable', 'professor_projects')
            ->withPivot(
                'jury_role',
                'votes',
                'is_president',
                'was_present',
                'supervision_status',
                'last_meeting_date',
                'next_meeting_date',
                'created_by',
                'updated_by',
                'approved_by',
            )
            ->using(\App\Models\ProfessorProject::class)
            ->withTimestamps();
    }

    // ...existing code...
}
