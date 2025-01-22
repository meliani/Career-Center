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
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use softDeletes;
    use TwoFactorAuthenticatable;

    protected $guard = 'students';

    protected $table = 'careers_backend.students';

    protected $appends = [
        'full_name',
        'long_full_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'id',
        'title',
        'id_pfe',
        'email',
        'first_name',
        'last_name',
        'email_perso',
        'phone',
        'cv',
        'lm',
        'birth_date',
        'level',
        'program',
        'is_mobility',
        'student_exchange_partner_id',
        'year_id',
        'is_active',
        'graduated_at',
        'avatar_url',
        'is_verified',
        'email_verified_at',
        'offers_viewed',
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
        'offers_viewed' => 'array',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // dd($panel->getId());
        if ($panel->getId() === 'research') {
            return $this->level === Enums\StudentLevel::MasterIoTBigData;
        }
        if ($panel->getId() === 'app' || str_ends_with($this->email, '@ine.inpt.ac.ma')) {
            return in_array($this->level, [
                Enums\StudentLevel::FirstYear,
                Enums\StudentLevel::SecondYear,
                Enums\StudentLevel::ThirdYear,
            ]);
        }

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
        // static::addGlobalScope(new Scopes\StudentScope);

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

        // send veryfication email
        $notification = new VerifyEmail;
        if ($this->level === Enums\StudentLevel::Research) {
            $notification->url = URL::temporarySignedRoute(
                'filament.research.auth.email-verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $student->getKey(),
                    'hash' => sha1($student->getEmailForVerification()),
                ],
            );
        } else {
            $notification->url = URL::temporarySignedRoute(
                'filament.app.auth.email-verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $student->getKey(),
                    'hash' => sha1($student->getEmailForVerification()),
                ],
            );
        }

        $student->notify($notification);

        // or use reset password
        // $token = app('auth.password.broker')->createToken($student);
        // $notification = new ResetPassword($token);
        // set panel for url
        // $notification->url = filament()->getPanel('app')->getResetPasswordUrl($token, $student);
        // $student->notify($notification);
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

    public function finalYearInternship()
    {
        return $this->hasOne(FinalYearInternshipAgreement::class);
    }

    // public function projects()
    // {
    //     return $this->belongsToMany(Project::class);
    // }

    // public function project()
    // {
    //     return Project::whereHas('students', function ($query) {
    //         $query->where('students.id', $this->id);
    //     })->first();
    // }

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

    public function getFormalNameAttribute()
    {
        return trim("{$this->title?->getLongTitle()} {$this->last_name} {$this->first_name}");
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getProgramCoordinator()
    {
        if ($this->level === Enums\StudentLevel::MasterIoTBigData) {

            return Professor::find(100024);
        }

        return Professor::where('role', Role::ProgramCoordinator)
            ->where('assigned_program', $this->program)
            ->first();
    }

    public function getProgramCoordinatorFormalName()
    {
        return $this->getProgramCoordinator() ? $this->getProgramCoordinator()->formal_name : __('Undefined');
    }

    public function applications()
    {
        return $this->hasMany(InternshipApplication::class);
    }

    public function applyToInternshipOffer(InternshipOffer $internshipOffer)
    {
        $this->applications()->create([
            'student_id' => $this->id,
            'internship_offer_id' => $internshipOffer->id,
        ]);
    }

    public function hasAppliedToInternshipOffer(InternshipOffer $internshipOffer)
    {
        return $this->applications()->where('internship_offer_id', $internshipOffer->id)->exists();
    }

    public function passTheYear($year, $level)
    {
        $this->year_id = $year->id;
        $this->level = $level;
        $this->save();
    }

    public function PassToNextLevel()
    {
        $this->level = $this->level->next();
        $this->save();
    }

    public function changeAcademicYear($year)
    {
        $this->year_id = $year;
        $this->save();
    }

    public function changeLevel($level)
    {
        $this->level = $level;
        $this->save();
    }

    /**
     * Check if student has viewed a specific offer
     *
     * @param  int  $offerId
     */
    public function hasViewedOffer($offerId): bool
    {
        return in_array($offerId, $this->offers_viewed ?? []);
    }

    /**
     * Mark an offer as viewed by the student
     *
     * @param  int  $offerId
     */
    public function markOfferAsViewed($offerId): void
    {
        if (! $this->hasViewedOffer($offerId)) {
            $viewedOffers = $this->offers_viewed ?? [];
            $viewedOffers[] = $offerId;
            $this->offers_viewed = array_unique($viewedOffers);
            $this->save();
        }
    }

    /**
     * Get count of viewed offers
     */
    public function getViewedOffersCount(): int
    {
        return count($this->offers_viewed ?? []);
    }

    /**
     * Get all viewed offers IDs
     */
    public function getViewedOffersIds(): array
    {
        return $this->offers_viewed ?? [];
    }

    /**
     * Clear viewed offers history
     */
    public function clearViewedOffers(): void
    {
        $this->offers_viewed = [];
        $this->save();
    }

    public function getAdministrativeSupervisorAttribute()
    {
        return User::where('role', Role::AdministrativeSupervisor)
            ->where('assigned_program', $this->program)
            ->first();
    }

    // Add these relationships to your Student model

    public function collaborationRequests()
    {
        return $this->hasMany(CollaborationRequest::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function sentCollaborationRequests()
    {
        return $this->hasMany(CollaborationRequest::class, 'sender_id');
    }

    public function receivedCollaborationRequests()
    {
        return $this->hasMany(CollaborationRequest::class, 'receiver_id');
    }

    // Add this relationship
    public function exchangePartner()
    {
        return $this->belongsTo(StudentExchangePartner::class, 'student_exchange_partner_id');
    }
}
