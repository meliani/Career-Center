<?php

namespace App\Models;

use Filament\Notifications\Notification;

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

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Scopes\TimetableScope());

        static::updating(function ($timetable) {
            $professorService = new \App\Services\ProfessorService;
            // we need to add actual slot to be excluded from the check
            $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $timetable->id);
            if (! $professor_availability) {
                Notification::make()
                    ->title(__('One of professors is not available in this timeslot, your operation is aborted'))
                    ->danger()
                    ->persistent()
                    // ->sendToDatabase(auth()->user());
                    ->send();

                return false; // This will abort the update operation.
            }
            $exists = self::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
                ->where('room_id', $timetable->room_id)
                ->where('id', '!=', $timetable->id)
                ->exists();
            if ($exists) {
                $existingTimetable = self::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
                    ->where('room_id', $timetable->room_id)
                    ->where('id', '!=', $timetable->id)
                    ->first();
                $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $existingTimetable->id);
                if (! $professor_availability) {
                    Notification::make()
                        ->title(__('One of professors is not available in this timeslot, your operation is aborted'))
                        ->danger()
                        ->persistent()
                        // ->sendToDatabase(auth()->user());
                        ->send();

                    return false; // This will abort the update operation.
                }
                // dd($timetable->timeslot->start_time, $timetable->room->name, $timetable->project_id);

                if ($existingTimetable->project_id !== null) {
                    Notification::make()
                        ->title(__('Timeslot :timeslot and Room :room conflict, your operation is aborted', ['timeslot' => $timetable->timeslot->start_time, 'room' => $timetable->room->name]))
                        ->danger()
                        ->persistent()
                        // ->sendToDatabase(auth()->user());
                        ->send();

                    return false; // This will abort the update operation.
                } else {
                    // dd($timetable->timeslot->start_time, $timetable->room->name, $timetable->project_id);
                    // self::update

                    return true;
                }
            } else {
                $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $timetable->id);
                if (! $professor_availability) {
                    Notification::make()
                        ->title(__('One of professors is not available in this timeslot, your operation is aborted'))
                        ->danger()
                        ->persistent()
                        // ->sendToDatabase(auth()->user());
                        ->send();

                    return false; // This will abort the update operation.
                }

                return true;
            }
        });
    }

    public function setProjectIdAttribute($projectId)
    {
        // remove the project_id from previous timetable
        // $this->where('project_id', $projectId)->update(['project_id' => null]);
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

    public function scopePlanned($query)
    {
        return $query->whereNotNull('project_id');
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
