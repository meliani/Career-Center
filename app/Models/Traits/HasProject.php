<?php

namespace App\Models\Traits;

use App\Models\FinalProject;
use App\Models\ProfessorProject;
use App\Models\Project;

trait HasProject
{
    public function projects()
    {
        if ($this instanceof \App\Models\Professor) {
            return $this->belongsToMany(Project::class, 'professor_projects')
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

        return $this->belongsToMany(Project::class);
    }

    public function finalProjects()
    {
        if ($this instanceof \App\Models\Professor) {
            return $this->belongsToMany(FinalProject::class, 'final_project_professor')
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

        return null;
    }

    public function project()
    {
        return Project::whereHas('students', function ($query) {
            $query->where('students.id', $this->id);
        })->first();
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
