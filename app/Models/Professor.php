<?php

namespace App\Models;

class Professor extends User
{
    protected $table = 'users';

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_professor')->withPivot('role');
    }
    // public function juries()
    // {
    //     return $this->belongsToMany(Jury::class, 'jury_professor')
    //         ->withTimestamps();
    // }
    public function juries()
    {
        return $this->belongsToMany(Jury::class, 'professor_jury')->withPivot('role');
    }
}
