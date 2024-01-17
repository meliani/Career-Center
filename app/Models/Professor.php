<?php

namespace App\Models;

class Professor extends User
{
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_professor')->withPivot('role');
    }
}
