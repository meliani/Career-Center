<?php

namespace App\Models;

use App\Enums;
use App\Models\Traits\HasProject;

class Professor extends User
{
    use HasProject;

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

    // Remove these methods as they're now in the trait:
    // - projects()
    // - allProjects()
    // - getProjectsCountAttribute()
    // - hasProjects()
}
