<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class Alumni extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $guard = 'alumnis';

    protected $table = 'alumnis';

    protected $appends = [
        'full_name',
        'long_full_name',
    ];

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
        'degree',
        'program',
        'is_enabled',
        'is_mobility',
        'abroad_school',
        'avatar_url',
        'number_of_bounces',
        'bounce_reason',
        'is_account_disabled',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return Auth::guard('alumnis')->check();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function canBeImpersonated()
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
        // return 'hello';
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getLongFullNameAttribute(): string
    {
        return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
    }

    public function graduationYear()
    {
        return $this->belongsTo(Year::class, 'graduation_year_id');
    }
}
