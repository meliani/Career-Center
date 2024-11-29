<?php

namespace App\Models\Traits;

use App\Models\FinalProject;
use App\Models\ProfessorProject;
use App\Models\Project;

trait HasProjects
{
    public function projects()
    {

        return $this->morphedByMany(Project::class, 'project', 'professor_projects', 'professor_id', 'project_id')
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
            ->withTimestamps()
            ->using(ProfessorProject::class);
    }

    public function finalProjects()
    {

        return $this->morphedByMany(FinalProject::class, 'project', 'professor_projects', 'professor_id', 'project_id')
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
            ->withTimestamps()
            ->using(ProfessorProject::class);
    }

    public function hasProjects()
    {
        return $this->projects()->exists();
    }

    public function getProjectsCountAttribute()
    {
        return $this->projects()->withoutGlobalScopes()->count();
    }

    public function allProjects()
    {
        return $this->projects()->withoutGlobalScopes();
    }
}
