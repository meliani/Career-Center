<?php

namespace App\Models;

use App\Enums;

class Professor extends User
{
    public static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query
                ->whereIn('role', Enums\Role::getProfessorRoles());
        });
        static::addGlobalScope(new Scopes\ProfessorScope);

    }

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'assigned_program',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Enums\Role::class,
        'department' => Enums\Department::class,
        'assigned_program' => Enums\Program::class,
        'title' => Enums\Title::class,
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'professor_projects')
            ->withPivot(
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
            )
            ->withTimestamps()
            ->using(ProfessorProject::class);
    }

    public function finalProjects()
    {
        return $this->belongsToMany(FinalProject::class, 'final_project_professor')
            ->withPivot(
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
            )
            ->withTimestamps()
            ->using(ProfessorProject::class);
    }

    public function allProjects()
    {
        return $this->projects()->withoutGlobalScopes();
    }

    public function getProjectsCountAttribute()
    {
        return $this->projects()->withoutGlobalScopes()->count();
    }

    public function hasProjects()
    {
        return $this->projects()->exists();
    }
}
