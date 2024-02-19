<?php

namespace App\Services;

use App\Models\Timetable;

class RoomService
{
    public static function checkRoomAvailability($timeslot, $room)
    {
        $timetables = Timetable::where('timeslot_id', $timeslot->id)
            ->where('room_id', $room->id)->get();

        if ($timetables->count() > 0) {
            return false;
        }

        return true;

    }
}
