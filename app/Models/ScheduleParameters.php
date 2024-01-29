<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleParameters extends Model
{
    protected $fillable = [
        'starting_from',
        'ending_at',
        'working_from',
        'working_to',
        'number_of_rooms',
        'max_defenses_per_professor',
        'max_rooms',
        'minutes_per_slot',
    ];
    protected $casts = [
        'starting_from' => 'date',
        'ending_at' => 'date',
        // 'working_from' => 'time',
        // 'working_to' => 'time',
    ];

}
