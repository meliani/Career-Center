<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ProfessorProject extends MorphPivot
{
    protected static $settings = true;

    protected $table = 'professor_projects'; // Correct table name

    protected $casts = [
        'jury_role' => Enums\JuryRole::class,
        'is_president' => 'boolean',
        'was_present' => 'boolean',
        'supervision_status' => Enums\SupervisionStatus::class,
        'last_meeting_date' => 'datetime',
        'next_meeting_date' => 'datetime',
    ];

    protected $fillable = [
        'professor_id',
        'jury_role',
        'votes',
        'is_president',
        'was_present',
        'created_by',
        'updated_by',
        'approved_by',
        'supervision_status',
        'last_meeting_date',
        'next_meeting_date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($professorProject) {
            $professorProject->created_by = auth()->id();
        });

        static::updating(function ($professorProject) {
            $professorProject->updated_by = auth()->id();
        });
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    // public function projectable()
    // {
    //     return $this->morphTo('professor_projectable');
    // }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function meetings()
    {
        return $this->hasMany(SupervisionMeeting::class);
    }

    public function progress_reports()
    {
        return $this->hasMany(ProgressReport::class);
    }

    // public function professorProjectable()
    // {
    //     return $this->morphTo();
    // }
}
