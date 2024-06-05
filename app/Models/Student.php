<?php

namespace App\Models;

use App\Enums;
use App\Enums\Role;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class Student extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    use MustVerifyEmail;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $guard = 'students';

    protected $table = 'students';

    public function canAccessPanel(Panel $panel): bool
    {
        // if ($panel->getId() === 'app') {
        //     return true;
        // }

        // return false;

        return str_ends_with($this->email, '@ine.inpt.ac.ma');
        // && $this->hasVerifiedEmail();

    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function canBeImpersonated()
    {
        return true;
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Scopes\StudentScope());

        // static::addGlobalScope(function ($query) {
        //     if (auth()->check()) {
        //         if (Auth::gate('students')) {
        //             $query->where('year_id', Year::current()->id)
        //                 ->where('is_active', true)
        //                 ->where('id', Auth::user()->id);
        //         } elseif (Auth::gate('web')) {
        //             $query->where('year_id', Year::current()->id)
        //                 ->where('is_active', true);
        //         }
        //     }

        // });
    }

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            $student->year_id = Year::current()->id;
            $student->name = $student->full_name;
            $student->is_verified = false;
        });

        static::created(function (Student $student) {
            $student->afterCreate();
        });
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
        // return 'hello';
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\User $user */
        $student = $this;

        //send veryfication email
        $notification = new VerifyEmail();
        $notification->url = URL::temporarySignedRoute(
            'filament.app.auth.email-verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $student->getKey(),
                'hash' => sha1($student->getEmailForVerification()),
            ],
        );
        $student->notify($notification);

        //or use reset password
        // $token = app('auth.password.broker')->createToken($student);
        // $notification = new ResetPassword($token);
        //set panel for url
        // $notification->url = filament()->getPanel('app')->getResetPasswordUrl($token, $student);
        // $student->notify($notification);
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
        'is_verified',
        'email_verified_at',
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
        'is_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
        return trim("{$this->title->getLabel()} {$this->first_name} {$this->last_name}");
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getProgramCoordinator()
    {
        return User::where('role', Role::ProgramCoordinator)
            ->where('program', $this->program)
            ->first();
    }
}
