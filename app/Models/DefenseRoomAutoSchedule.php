<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class DefenseRoomAutoSchedule extends Model
{
    protected $fillable = [
        'room_id',
        'timeslot_id',
        'project_id',
        'score',

    ];
    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
