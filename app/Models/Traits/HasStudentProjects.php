<?php

namespace App\Models\Traits;

use App\Models\FinalProject;
use App\Models\Project;
use App\Models\ProjectStudent;

trait HasStudentProjects
{
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'project', 'project_student', 'student_id', 'project_id')
            ->using(ProjectStudent::class)
            ->withTimestamps();
    }

    public function project()
    {
        return $this->projects();
    }

    public function finalProjects()
    {
        return $this->morphedByMany(FinalProject::class, 'project', 'project_student', 'student_id', 'project_id')
            ->withPivot('id')
            ->using(ProjectStudent::class)
            ->withTimestamps();
    }

    public function currentProject()
    {
        return $this->projects()
            ->orWhere(function ($query) {
                $query->whereHasMorph('project', [Project::class, FinalProject::class]);
            })
            ->first();
    }

    public function hasActiveProject()
    {
        return $this->projects()->exists() || $this->finalProjects()->exists();
    }
}
