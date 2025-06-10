<?php

namespace App\Models;

use App\Enums;
use App\Models\Traits\ProjectAttributes;
use App\Notifications;
use App\Services;
use Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
// soft delete
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Core\BackendBaseModel
{
    use HasFilamentComments;
    use ProjectAttributes;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        // static::addGlobalScope(new Scopes\ProjectScope);
    }

    protected static function booted(): void
    {
        /*
            static::updated(function ($project) {
                $admins = \App\Models\User::administrators();
                Notification::send($admins, new Notifications\ProjectUpdated($project));
            });
        */
    }

    // rules
    public static function rules($id = null)
    {
        return [
            'title' => 'required|max:255',
            'writing_language' => 'nullable|string|in:' . implode(',', array_column(Enums\Language::cases(), 'value')),
            'presentation_language' => 'nullable|string|in:' . implode(',', array_column(Enums\Language::cases(), 'value')),
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    }

    protected $fillable = [
        'title',
        'language',
        'writing_language',
        'presentation_language',
        'start_date',
        'end_date',
        'agreement_verified_at',
        'agreement_verified_by',
        'supervisor_approved_at',
        'supervisor_approved_by',
        'organization_evaluation_received_at',
        'organization_evaluation_received_by',
        'defense_status',
        'defense_authorized_at',
        'defense_authorized_by',
        'evaluation_sheet_url',
        'organization_evaluation_sheet_url',
        'midterm_due_date',
        'midterm_report_status',
        'defense_link',
    ];

    protected $appends = [
        'id_pfe',
        // 'organization',
        // 'description',
        'assigned_departments',
        'agreement_types',
        'students_names',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'midterm_due_date' => 'date',
        'defense_authorized_at' => 'datetime',
        'language' => Enums\Language::class,
        'writing_language' => Enums\Language::class,
        'presentation_language' => Enums\Language::class,
        'defense_status' => Enums\DefenseStatus::class,
        'midterm_report_status' => Enums\MidTermReportStatus::class,
    ];

    // public function internship_agreements()
    // {
    //     return $this->morphedByMany(InternshipAgreement::class, 'agreeable', 'project_agreements')
    //         ->using(ProjectAgreement::class)
    //         ->withTimestamps();
    // }

    // public function final_internship_agreement()
    // {
    //     return $this->morphedByMany(FinalYearInternshipAgreement::class, 'agreeable', 'project_agreements')
    //         ->using(ProjectAgreement::class)
    //         ->withTimestamps();
    // }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_projects')
            ->withPivot('jury_role', 'created_by', 'updated_by', 'approved_by', 'is_president', 'votes', 'was_present')
            ->withTimestamps()
            ->using(ProfessorProject::class);

    }

    public function hasTeammate()
    {
        return $this->internship_agreements()->count() > 1;
    }

    public function academic_supervisor()
    {
        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value)
            ->first();
    }

    public function first_reviewer()
    {
        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::FirstReviewer->value)
            ->first();
    }

    public function second_reviewer()
    {
        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::SecondReviewer->value)
            ->first();
    }

    public function reviewers()
    {
        return $this->professors()
            ->whereIn('jury_role', [Enums\JuryRole::FirstReviewer->value, Enums\JuryRole::SecondReviewer->value]);
    }

    public function timetable()
    {
        return $this->hasOne(Timetable::class)
            // ->whereNull('cancelled_at')
            ->latest();
    }

    // Relationship: Only timetable for the current year
    public function currentYearTimetable()
    {
        return $this->hasOne(Timetable::class)
            ->whereHas('timeslot', function($query) {
                $query->whereYear('date', now()->year);
            })
            // ->whereNull('cancelled_at')
            ->latest();
    }

    public function unplanned()
    {
        return $this->whereDoesntHave('timetable');
    }

    public function defense_authorized_by_user()
    {
        return $this->belongsTo(User::class, 'defense_authorized_by', 'id');
    }

    public function authorizeDefense()
    {
        try {
            if (Gate::denies('authorize-defense', $this)) {
                throw new AuthorizationException;
            }

            $this->defense_authorized_at = now();
            $this->defense_authorized_by = auth()->id();
            $this->defense_status = Enums\DefenseStatus::Authorized;

            $this->generateEvaluationSheet();
            $this->save();

            Filament\Notifications\Notification::make()
                ->title('Defense has been authorized successfully.')
                ->success()
                ->send();

            return $this;
        } catch (AuthorizationException $e) {
            Filament\Notifications\Notification::make()
                ->title('Sorry You dont have the permission to do this action.')
                ->danger()
                ->send();

            throw $e;
        }
    }

    public function postponeDefense()
    {
        try {
            if (Gate::denies('authorize-defense', $this)) {
                throw new AuthorizationException;
            }
            $this->defense_status = Enums\DefenseStatus::Postponed;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Defense has been postponed successfully.')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {
            Filament\Notifications\Notification::make()
                ->title('Sorry You dont have the permission to do this action.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function completeDefense()
    {
        try {
            if (Gate::denies('authorize-defense', $this)) {
                throw new AuthorizationException;
            }
            $this->defense_status = Enums\DefenseStatus::Completed;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Defense has been completed successfully.')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {
            Filament\Notifications\Notification::make()
                ->title('Sorry You dont have the permission to do this action.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function markAllProfessorsAsPresent()
    {
        $this->professors()->each(function ($professor) {
            $this->professors()->updateExistingPivot($professor->id, ['was_present' => true]);
        });
    }

    public function generateEvaluationSheet()
    {
        // we gonna use GenerateDefenseDocuments service and generateEvaluationSheet method to generate the evaluation sheet

        $service = new Services\GenerateDefenseDocuments;
        $service->generateEvaluationSheet($this);

        // event(new \App\Events\DefenseAuthorized($this));

    }

    public function isAuthorized()
    {
        return $this->defense_status == Enums\DefenseStatus::Authorized;
    }

    public function agreements()
    {
        return $this->hasMany(ProjectAgreement::class)->with('agreeable.student');
    }

    public function final_internship_agreements()
    {
        return $this->morphedByMany(FinalYearInternshipAgreement::class, 'agreeable', 'project_agreements');
    }

    public function getAgreementTypesAttribute()
    {
        return $this->agreements
            ->pluck('agreeable_type')
            ->map(function ($type) {
                return class_basename($type);
            })
            ->unique()
            ->values()
            ->toArray();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function externalSupervisor()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'external_supervisor_id');
    }

    public function parrain()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'parrain_id');
    }

    // active scope
    public function scopeActive($query)
    {
        $currentYear = Year::current();
        if (!$currentYear) {
            return $query->whereRaw('1 = 0'); // Return empty result if no current year
        }
        $currentYearId = $currentYear->id;
        
        return $query->whereNot('defense_status', Enums\DefenseStatus::Completed)
            ->whereHas('agreements', function ($query) use ($currentYearId) {
                $query->whereMorphRelation(
                    'agreeable',
                    [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                    'year_id',
                    $currentYearId
                );
            });
    }
    
    // Add a new scope to filter projects by year
    public function scopeForYear($query, $yearId = null)
    {
        $currentYear = Year::current();
        $yearId = $yearId ?? ($currentYear ? $currentYear->id : null);
        
        if (!$yearId) {
            return $query->whereRaw('1 = 0'); // Return empty result if no year
        }
        
        return $query->whereHas('agreements', function ($query) use ($yearId) {
            $query->whereMorphRelation(
                'agreeable',
                '*',
                'year_id',
                $yearId
            );
        });
    }

    // Add a helper method to check if project is for current year
    public function isCurrentYear(): bool
    {
        $currentYear = Year::current();
        if (!$currentYear) {
            return false;
        }
        $currentYearId = $currentYear->id;
        
        return $this->agreements()
            ->whereMorphRelation(
                'agreeable',
                '*',
                'year_id',
                $currentYearId
            )->exists();
    }

    public function canAddCollaborator()
    {
        return $this->agreements->count() < 2;
    }

    public function addCollaborator(Student $student): void
    {
        // Check if we can add more collaborators
        if (! $this->canAddCollaborator()) {
            throw new \Exception('This project already has the maximum number of collaborators.');
        }

        $agreement = $student->finalYearInternship;

        // Link the agreement to this project
        ProjectAgreement::updateOrCreate(
            [
                'agreeable_id' => $agreement->id,
                'agreeable_type' => FinalYearInternshipAgreement::class,
            ],
            [
                'project_id' => $this->id,
            ]
        );
    }

    public function suggestedInternalSupervisor()
    {
        return $this->final_internship_agreements()
            ->first()
            ->suggestedInternalSupervisor;
    }

    public function midTermReports()
    {
        return $this->hasMany(MidTermReport::class);
    }

    // public function FirstReviewer()
    // {
    //     return $this->professors()
    //         ->wherePivot('jury_role', Enums\JuryRole::FirstReviewer->value);
    // }

    // public function SecondReviewer()
    // {
    //     return $this->professors()
    //         ->wherePivot('jury_role', Enums\JuryRole::SecondReviewer->value);
    // }
}
