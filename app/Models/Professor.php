<?php

namespace App\Models;

class Professor extends User
{
    protected $table = 'users';
    
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_professor')->withPivot('role');
    }
}
