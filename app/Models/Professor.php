<?php

namespace App\Models;

use App\Enums;

class Professor extends User
{
    public static function boot(){
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query
            //  where role is in this array Enums\Role::getProfessorRoles()
            
                ->whereIn('role' ,Enums\Role::getProfessorRoles());
        });
    }
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'program_coordinator',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Enums\Role::class,
        'department' => Enums\Department::class,
        'program_coordinator' => Enums\Program::class,
    ];
    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('jury_role');
    }
}
