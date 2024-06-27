<?php

namespace App\Services;

use App\Models\Timetable;

class RoomService
{
    public static function checkRoomAvailability($timeslot, $room, $currentTimetableId = null)
    {
        // Build the query to check for existing records with the same timeslot_id and room_id
        $query = Timetable::where('timeslot_id', $timeslot->id)
            ->where('room_id', $room->id);

        // If updating an existing entry, exclude it from the check
        if ($currentTimetableId !== null) {
            $query->where('id', '!=', $currentTimetableId);
        }

        // Check if a record exists that matches the query
        $exists = $query->exists();

        // Return false if a record exists (room not available), true otherwise (room available)
        return ! $exists;
    }
}
