<?php

namespace App\Models;

class Timetable extends Core\BackendBaseModel
{
    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function professors()
    {
        // return $this->hasManyThrough(
        //     Deployment::class,
        //     Environment::class,
        //     'project_id', // Foreign key on the environments table...
        //     'environment_id', // Foreign key on the deployments table...
        //     'id', // Local key on the projects table...
        //     'id' // Local key on the environments table...
        // );
        return $this->hasManyThrough(Professor::class, Project::class,
            'id', // Foreign key on the projects table...
            'id', // Foreign key on the professors table...
            'project_id', // Local key on the timetables table...
            'id' // Local key on the projects table...
        );

    }
}
