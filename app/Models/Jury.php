<?php

namespace App\Models;

class Jury extends User
{    
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_jury');
    }
}
