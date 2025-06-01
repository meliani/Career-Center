<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Core\BackendBaseModel
{
    protected $fillable = [
        'timeslot_id',
        'room_id',
        'project_id',
        'year_id',
        'scheduled_by',
    ];

    protected static function boot()
    {
        parent::boot();
        // static::addGlobalScope(new Scopes\TimetableScope);

        /*         static::updating(function ($timetable) {
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
                }); */
    }

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

    // Add status accessors
    public function getIsEnabledAttribute(): bool
    {
        return ! is_null($this->confirmed_at);
    }

    // Status accessors
    public function getIsTakenAttribute(): bool
    {
        return ! is_null($this->project_id);
    }

    // Relationships
    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(Timeslot::class)->orderBy('start_time', 'asc');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->available();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    public function available_timeslots()
    {
        return $this->where('project_id', null);
    }

    // public function unplanned_timetable()
    // {
    //     return $this->whereDoesntHave('project');
    // }

    // Scopes
    public function scopeUnplanned($query)
    {
        return $query->whereDoesntHave('project');
        // return $query->whereNull('project_id');
    }

    public function scopePlanned($query)
    {

        // return $query->whereHas('project');
        return $query->whereNotNull('project_id');
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

    public function schedulable()
    {
        return $this->morphTo();
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    // Add date accessor
    public function getDateAttribute()
    {
        return $this->timeslot?->date;
    }

    public function getStartTimeAttribute()
    {
        return $this->timeslot?->start_time;
    }

    public function getEndTimeAttribute()
    {
        return $this->timeslot?->end_time;
    }
}
