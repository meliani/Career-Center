<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use \App\Enums\Role;
use \App\Enums\Department;
use \App\Enums\Program;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    protected $administrators = [ Role::SuperAdministrator , Role::Administrator];
    protected $professors = [ Role::SuperAdministrator, Role::Administrator, Role::Professor, Role::HeadOfDepartment, Role::ProgramCoordinator];
    protected $powerProfessors = [Role::SuperAdministrator, Role::Administrator, Role::ProgramCoordinator];

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'role',
        'department',
        'program_coordinator',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $appends = [
    // ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
        'department' => Department::class,
        'program_coordinator' => Program::class,
    ];

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasRole(Role $role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'Administration') {
            return $this->hasAnyRole($this->administrators);
        }
        if ($panel->getId() === 'ProgramCoordinator') {
            return $this->haAnyRole($this->powerProfessors);
        }

        // return str_ends_with($this->email, '@inpt.ac.ma') && $this->hasVerifiedEmail();
        return true;
    }

    public function isSuperAdministrator()
    {
        return $this->hasRole(Role::SuperAdministrator);
    }
    public function isAdministrator()
    {
        return $this->hasAnyRole($this->administrators);
    }
}
