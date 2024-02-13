<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LaraZeus\Boredom\Concerns\HasBoringAvatar;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, HasBoringAvatar;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $administrators = [Enums\Role::SuperAdministrator, Enums\Role::Administrator];

    protected $professors = [Enums\Role::SuperAdministrator, Enums\Role::Administrator, Enums\Role::Professor, Enums\Role::DepartmentHead, Enums\Role::ProgramCoordinator];

    protected $powerProfessors = [Enums\Role::SuperAdministrator, Enums\Role::Administrator, Enums\Role::ProgramCoordinator];

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'department',
        'role',
        'email',
        'program_coordinator',
        'is_enabled',
        'email_verified_at',
        'password',
        'remember_token',
        'active_status',
        'avatar',
        'dark_mode',
        'messenger_color',
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
        'role' => Enums\Role::class,
        'department' => Enums\Department::class,
        'program_coordinator' => Enums\Program::class,
        'title' => Enums\Title::class,
    ];
    protected $appends = [
        'long_full_name',
    ];

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getLongFullNameAttribute()
    {
        return "{$this->title->getLabel()} {$this->first_name} {$this->last_name}";
    }
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasRole(Enums\Role $role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        /* if ($panel->getId() === 'Administration') {
            return $this->hasAnyRole($this->administrators);
        }
        if ($panel->getId() === 'ProgramCoordinator') {
            return $this->haAnyRole($this->powerProfessors);
        } */
        if ($panel->getId() === 'Administration') {
            return $this->hasAnyRole(Enums\Role::getAll());
        }

        // return str_ends_with($this->email, '@inpt.ac.ma') && $this->hasVerifiedEmail();
        return true;
    }
    public function isSuperAdministrator()
    {
        return $this->hasRole(Enums\Role::SuperAdministrator);
    }

    public function isAdministrator()
    {
        return $this->hasAnyRole($this->administrators);
    }
    public function isProfessor()
    {
        return $this->hasAnyRole($this->professors);
    }
    public function isProgramCoordinator()
    {
        return $this->hasAnyRole($this->powerProfessors);
    }
    public function isDepartmentHead()
    {
        return $this->hasRole(Enums\Role::DepartmentHead);
    }
}
