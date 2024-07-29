<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class Alumni extends Authenticatable implements FilamentUser, HasAvatar, HasName, MustVerifyEmailContract
{
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $guard = 'alumnis';

    protected $table = 'alumnis';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'title',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'graduation_year_id',
        'graduation_degree',
        'degree',
        'program',
        'is_verified',
        'verified_at',
        'verified_by',
        'work_status',
        'is_mobility',
        'abroad_school',
        'avatar_url',
        'number_of_bounces',
        'bounce_reason',
        'is_account_disabled',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_mobility' => 'boolean',
        'is_account_disabled' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return Auth::guard('alumnis')->check();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::disk('alumni-profile-photos')->url($this->avatar_url) : null;
    }

    public function canBeImpersonated()
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function graduationYear()
    {
        return $this->belongsTo(Year::class, 'graduation_year_id');
    }

    public function isVerified()
    {
        return $this->is_verified;
    }
}
