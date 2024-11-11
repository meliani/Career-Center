<?php

namespace App\Models;

use Carbon\Carbon;

class ScheduleParameters extends Core\BackendBaseModel
{
    protected $fillable = [
        'schedule_starting_at',
        'schedule_ending_at',
        'day_starting_at',
        'day_ending_at',
        'number_of_rooms',
        'max_defenses_per_professor',
        'max_rooms',
        'minutes_per_slot',
    ];

    protected $casts = [
        'schedule_starting_at' => 'datetime',
        'schedule_ending_at' => 'datetime',
        'day_starting_at' => 'datetime',
        'day_ending_at' => 'datetime',
    ];
    // public function getDayEndingAtAttribute($value)
    // {
    //     return Carbon::createFromFormat('H:i:s', $value);
    // }
    // public function getDayStartingAtAttribute($value)
    // {
    //     return Carbon::parse($value);
    // }

}
