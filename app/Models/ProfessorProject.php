<?php

namespace App\Models;

use App\Enums;
use App\Notifications;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Notification;

class ProfessorProject extends Pivot
{
    protected static $settings = true;

    // public function __construct(?\App\Settings\NotificationSettings $settings = null)
    // {
    //     self::$settings = $settings;
    // }

    protected $casts = [
        'jury_role' => Enums\JuryRole::class,
        'is_president' => 'boolean',
    ];

    protected $fillable = [
        'jury_role',
        'votes',
        'is_president',
        'created_by',
        'updated_by',
        'approved_by',
    ];

    protected static function booted(): void
    {

        static::creating(function ($professorProject) {
            $professorProject->created_by = auth()->id();

        });

        static::updating(function ($professorProject) {
            $professorProject->updated_by = auth()->id();
        });

        static::created(function ($professorProject) {
            if ($professorProject->assigned_by) {
                $professorProject->assigned_by->notify(new Notifications\ProjectSupervisorCreated($professorProject));
            }
            $admins = \App\Models\User::administrators();
            Notification::send($admins, new Notifications\ProjectSupervisorCreated($professorProject));
        });

    }

    public function ApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(Professor::class, 'professor_project_reviewer', 'professor_project_id', 'professor_id')
            ->withPivot('created_by', 'updated_by', 'approved_by')
            ->withTimestamps();
    }

    public function supervisor()
    {
        return $this->hasOne(Professor::class, 'professor_project_id');

        // return $this->belongsTo(Professor::class, 'professor_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
