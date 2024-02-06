<?php

namespace App\Models;

use App\Enums\Room as RoomEnum;

class Timeslot extends Core\BackendBaseModel
{
    protected $remaining_slots;
    protected $scheduleParameters;

    // public function __construct(array $attributes = [], ScheduleParameters $scheduleParameters = null)
    // {
    //     parent::__construct($attributes);
    //     $this->scheduleParameters = $scheduleParameters;
    //     if ($this->scheduleParameters == null) {
    //         $scheduleParameters = ScheduleParameters::first();
    //     } else {
    //         $scheduleParameters = $this->scheduleParameters;
    //     }
    //     $this->remaining_slots = $scheduleParameters->number_of_rooms;
    // }
    protected $fillable = [
        'start_time',
        'end_time',
        'is_enabled',
        'is_taken',
        'remaining_slots',
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_enabled' => 'boolean',
        'is_taken' => 'boolean',
    ];
//     protected $attributes = [
//         'is_enabled' => true,
//         'is_taken' => false,
//     ];
}
