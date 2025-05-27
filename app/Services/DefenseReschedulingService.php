<?php

namespace App\Services;

use App\Models\RescheduleRequest;
use App\Models\Timetable;
use App\Models\Timeslot;
use App\Models\Room;
use App\Services\ProfessorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefenseReschedulingService
{
    /**
     * Handle the rescheduling of a defense based on an approved request
     * 
     * @param RescheduleRequest $request The approved reschedule request
     * @return Timetable|null The new timetable entry if successful, null otherwise
     */
    public function rescheduleDefense(RescheduleRequest $request): ?Timetable
    {
        // Verify that the request is approved
        if ($request->status->value !== 'approved') {
            return null;
        }
        
        // Get the current timetable entry and related project
        $currentTimetable = $request->timetable;
        $project = $currentTimetable->project;
        $student = $request->student;
        
        try {
            DB::beginTransaction();
              // Get the preferred timeslot from the request
            $timeslot = $request->preferredTimeslot;
            
            // If no timeslot is found, return null (should not happen with proper validation)
            if (!$timeslot) {
                DB::rollBack();
                return null;
            }
            
            // Double-check professor availability to make sure they're still available
            // since the request might have been submitted some time ago
            $professorsAvailable = ProfessorService::checkJuryAvailability(
                $timeslot, 
                $project, 
                $currentTimetable->id
            );
            
            if (!$professorsAvailable) {
                DB::rollBack();
                Log::error('Defense rescheduling failed: professors are no longer available at this timeslot');
                return null;
            }
            
            // Find an available room for this timeslot
            $usedRoomIds = Timetable::where('timeslot_id', $timeslot->id)
                ->pluck('room_id')
                ->toArray();
                
            $availableRoom = Room::where('status', \App\Enums\RoomStatus::Available)
                ->whereNotIn('id', $usedRoomIds)
                ->first();
                
            // If no room is available, use the same room as before
            if (!$availableRoom) {
                $availableRoom = $currentTimetable->room;
            }
              // Create a new timetable entry for the rescheduled defense
            $newTimetable = Timetable::create([
                'project_id' => $project->id,
                'room_id' => $availableRoom->id,
                'timeslot_id' => $timeslot->id,
                'scheduled_by' => $request->processed_by,
            ]);
            
            // Optionally, mark the old timetable as inactive or delete it
            $currentTimetable->delete();
              // Update the request to indicate it's been fulfilled
            $request->update([
                'admin_notes' => ($request->admin_notes ? $request->admin_notes . "\n\n" : '') . 
                    "Defense successfully rescheduled to {$timeslot->start_time->format('F j, Y')} at {$timeslot->start_time->format('H:i')} in room {$availableRoom->name}."
            ]);
            
            DB::commit();
            
            return $newTimetable;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Defense rescheduling failed: ' . $e->getMessage());
            return null;
        }
    }
}
