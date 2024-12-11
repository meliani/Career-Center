<?php

namespace App\Models;

use App\Enums;
use App\Models\Traits\ProjectAttributes;
use App\Notifications;
use App\Services;
use Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Core\BackendBaseModel
{
    use HasFilamentComments;
    use ProjectAttributes;

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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    }

    protected $fillable = [
        'title',
        'language',
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
    ];

    protected $appends = [
        'id_pfe',
        // 'organization',
        // 'description',
        'assigned_departments',
        'agreement_types',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'language' => Enums\Language::class,
        'defense_status' => Enums\DefenseStatus::class,
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

    // public function AcademicSupervisor()
    // {

    //     return $this->professors()
    //         ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value);
    // }

    public function reviewers()
    {

        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::FirstReviewer->value)
            ->orWherePivot('jury_role', Enums\JuryRole::SecondReviewer->value);
    }

    public function timetable()
    {
        return $this->hasOne(Timetable::class);
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
            // if (! $this->evaluation_sheet_url) {
            $this->generateEvaluationSheet();
            // }
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Defense has been authorized successfully.')
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
