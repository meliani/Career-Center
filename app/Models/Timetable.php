<?php

namespace App\Models;

class Timetable extends Core\BackendBaseModel
{
    protected $fillable = [
        'timeslot_id',
        'room_id',
        'project_id',
        'user_id',
        'is_enabled',
        'is_taken',
        'is_confirmed',
        'is_cancelled',
        'is_rescheduled',
        'is_deleted',
        'confirmed_at',
        'cancelled_at',
        'rescheduled_at',
        'deleted_at',
        'confirmed_by',
        'cancelled_by',
        'rescheduled_by',
        'deleted_by',
        'created_by',
        'updated_by',
    ];

    public function setProjectIdAttribute($projectId)
    {
        // remove the project_id from previous timetable
        $this->where('project_id', $projectId)->update(['project_id' => null]);
        $this->attributes['project_id'] = $projectId;

    }

    public function setTimeslotIdAttribute($timeslotId)
    {

        $this->attributes['timeslot_id'] = $timeslotId;
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }

    public function available_timeslots()
    {
        return $this->where('project_id', null);
    }

    // public function unplanned_timetable()
    // {
    //     return $this->whereDoesntHave('project');
    // }

    public function scopeUnplanned($query)
    {
        // return $query->whereDoesntHave('project');
        return $query->whereNull('project_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class)->enabled();
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
        return $this->hasManyThrough(
            Professor::class,
            Project::class,
            'id', // Foreign key on the projects table...
            'id', // Foreign key on the professors table...
            'project_id', // Local key on the timetables table...
            'id' // Local key on the projects table...
        );

    }
}
