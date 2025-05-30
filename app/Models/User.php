<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $administrators = [Enums\Role::SuperAdministrator, Enums\Role::Administrator, Enums\Role::System];

    protected $professors = [Enums\Role::Professor, Enums\Role::DepartmentHead, Enums\Role::ProgramCoordinator];

    protected $powerProfessors = [Enums\Role::ProgramCoordinator, Enums\Role::DepartmentHead];

    protected $fillable = [
        'title',
        'name',
        'first_name',
        'last_name',
        'department',
        'role',
        'email',
        'assigned_program',
        'is_enabled',
        'can_supervise',
        'email_verified_at',
        'password',
        'remember_token',
        'active_status',
        'avatar_url',
        'avatar',
        'dark_mode',
        'messenger_color',
        'last_login_at',
    ];

    protected $connection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('database.default');
        $this->table = env('DB_DATABASE', 'careers_backend') . '.users';
    }
    // protected static function boot(): void
    // {
    //     parent::boot();
    // }

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
        'assigned_program' => Enums\Program::class,
        'title' => Enums\Title::class,
        'getting_started_steps' => 'array',
        'last_login_at' => 'datetime',
    ];

    protected $appends = [
        'long_full_name',
        'full_name',
    ];

    public function canImpersonate()
    {
        return $this->isSuperAdministrator() || $this->isAdministrator();
    }

    public function canBeImpersonated()
    {
        // Let's prevent impersonating other users at our own company
        // return !Str::endsWith($this->email, '@mycorp.com');
        return ! $this->isSuperAdministrator();
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
        // elseif ($panel->getId() === 'app') {
        //     return $this->hasAnyRole(Enums\Role::getAll());
        // }

        // return str_ends_with($this->email, '@inpt.ac.ma') && $this->hasVerifiedEmail();
        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
        // return $this->avatar_url ? Storage::url($this->avatar_url) : asset('/svg/logo_entreprises_round.svg');
    }

    // add scope for administrators role
    public function scopeAdministrators($query)
    {
        return $query->whereIn('role', $this->administrators);
    }

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
        return trim("{$this->title?->getLabel()} {$this->first_name} {$this->last_name}");
    }

    public function getFormalNameAttribute()
    {
        return trim("{$this->title?->getLongTitle()} {$this->last_name} {$this->first_name}");
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
        return $this->hasRole(Enums\Role::ProgramCoordinator);
    }

    public function isDepartmentHead()
    {
        return $this->hasRole(Enums\Role::DepartmentHead);
    }

    public function isPowerProfessor()
    {
        return $this->hasAnyRole($this->powerProfessors);
    }

    public function isDirection()
    {
        return $this->hasRole(Enums\Role::Direction);
    }

    public function isAdministrativeSupervisor($id = null)
    {
        if ($id) {
            return $this->id === $id;
        }

        return $this->hasRole(Enums\Role::AdministrativeSupervisor);
    }

    // a function to get administrativeSupervisor from assigned program

    public static function administrativeSupervisor($program)
    {
        $adminSupervisor = User::where('assigned_program', $program)->where('role', Enums\Role::AdministrativeSupervisor->value)->first();

        return $adminSupervisor;
    }

    public function isRoot()
    {
        return $this->hasRole(Enums\Role::System) || $this->id === 1;
    }

    public function sendPasswordResetNotification($token)
    {
        $url = \Illuminate\Support\Facades\URL::secure(route('password.reset', ['token' => $token, 'email' => $this->email]));

        $this->notify(new \Visualbuilder\EmailTemplates\Notifications\UserResetPasswordRequestNotification($url));
    }
}
