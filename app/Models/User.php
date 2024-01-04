<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasName;
use App\Enums\Role;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    protected $fillable = [
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'role' => Role::class,
    ];
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        if ($this->role) {
            return in_array($this->role, $roles);
        }

        return false;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return str_ends_with($this->email, '@inpt.ac.ma') && $this->hasVerifiedEmail();
    }
}
