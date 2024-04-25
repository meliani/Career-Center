<?php

namespace App\Models;

use App\Enums;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class Student extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'app') {
            return true;
        }

        // return false;

        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();

    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function canBeImpersonated()
    {
        return true;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\StudentScope());

        static::creating(function (Student $student) {
            $student->year_id = Year::current()->id;
            $student->name = $student->full_name;
        });
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
        // return 'hello';
    }

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
        'pin',
        'email',
        'first_name',
        'last_name',
        'email_perso',
        'phone',
        'cv',
        'lm',
        'photo',
        'birth_date',
        'level',
        'program',
        'is_mobility',
        'abroad_school',
        'year_id',
        'is_active',
        'graduated_at',
        'avatar_url',
    ];

    protected $casts = [
        'title' => Enums\Title::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'program' => Enums\Program::class,
        'level' => Enums\StudentLevel::class,

    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query
                ->where('year_id', 7);
        });
    }

    public function routeNotificationForMail(): array | string
    {
        // Return email address only...
        return $this->email;
        // return [$this->email, $this->email_perso];

        // Return email address and name...
        // return [$this->email_address => $this->full_name];
    }

    public function setPin(Student $student, $currentPin, $streamOrder)
    {
        $student->pin = $streamOrder . str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    public function internship()
    {
        return $this->hasOne(InternshipAgreement::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function project()
    {
        return Project::whereHas('students', function ($query) {
            $query->where('students.id', $this->id);
        })->first();
    }

    public function teammate()
    {
        if (! $this->project()->hasTeammate()) {
            return null;
        }

        return Student::whereHas('projects', function ($query) {
            $query->where('projects.id', $this->project()->id)
                ->where('student_id', '!=', $this->id);
        })->first();
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function active_internship_agreement()
    {
        return $this->hasOne(InternshipAgreement::class);

        // return $this->hasOne(InternshipAgreement::class)->ofMany([
        //     'published_at' => 'max',
        //     'id' => 'max',
        // ], function (Builder $query) {
        //     $query->where('active', '=', true);
        // });
    }

    public function inactiveInternshipAgreements()
    {
        return $this->hasMany(InternshipAgreement::class)->where('active', false);
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getLongFullNameAttribute()
    {
        return $this->title->getLabel() . '. ' . $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
