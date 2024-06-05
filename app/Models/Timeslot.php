<?php

namespace App\Models;

use Carbon\Carbon;

class Timeslot extends Core\BackendBaseModel
{
    protected $scheduleParameters;

    public function __construct(?Carbon $start_time = null, ?Carbon $end_time = null, $is_enabled = true)
    {
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->is_enabled = $is_enabled;
    }

    protected $fillable = [
        'start_time',
        'end_time',
        'is_enabled',
    ];

    protected $casts = [
        // 'start_time' => 'date:Y-m-d H:i:s',
        // 'end_time' => 'date:Y-m-d H:i:s',
        'is_enabled' => 'boolean',
        'is_taken' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // protected $dates = [
    //     'start_time',
    //     'end_time',
    // ];
    //     protected $attributes = [
    //         'is_enabled' => true,
    //         'is_taken' => false,
    //     ];

    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }
}
