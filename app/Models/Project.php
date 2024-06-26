<?php

namespace App\Models;

use App\Enums;
use App\Notifications;
use App\Services;
use Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Core\BackendBaseModel
{
    use HasFilamentComments;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Scopes\ProjectScope());
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
        'organization',
        'description',
        'assigned_department',
    ];

    protected $casts = [
        'start_date' => 'date:Y/m/d',
        'end_date' => 'date:Y/m/d',
        'language' => Enums\Language::class,
        'defense_status' => Enums\DefenseStatus::class,
    ];

    public function getIdPfeAttribute()
    {
        if ($this->hasTeammate()) {
            return ($this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID') . ' ' . __('&') . ' ' . ($this->internship_agreements()->latest()->first() ? $this->internship_agreements()->latest()->first()->id_pfe : 'Undefined ID');
        } else {
            return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID';
        }
    }

    public function getOrganizationAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->organization_name : 'Undefined Organization';

    }

    public function getDescriptionAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->description : 'Undefined Internship';
    }

    public function getAssignedDepartmentAttribute()
    {

        if ($this->hasTeammate()) {
            return $this->internship_agreements()->first()?->assigned_department;

            return $this->internship_agreements()->first()?->assigned_department . ' ' . __('&') . ' ' . $this->internship_agreements()->latest()->first()?->assigned_department;
        } else {
            return $this->internship_agreements()->first()?->assigned_department;
        }
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function internship_agreements()
    {
        return $this->hasMany(InternshipAgreement::class);
    }

    public function internship_agreement()
    {
        // return $this->internship_agreements()->first();
        return $this->hasOne(InternshipAgreement::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_project')
            ->withPivot('jury_role', 'created_by', 'updated_by', 'approved_by', 'is_president', 'votes')->withTimestamps()
            ->using(ProfessorProject::class);

    }

    public function hasTeammate()
    {
        return $this->students()->count() > 1;
    }

    public function supervisor()
    {
        // dd($this->professors()
        //     ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value));

        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value);
    }

    public function reviewers()
    {

        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer->value)
            ->orWherePivot('jury_role', Enums\JuryRole::Reviewer1->value)
            ->orWherePivot('jury_role', Enums\JuryRole::Reviewer2->value);
    }

    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }

    public function unplanned()
    {
        return $this->whereDoesntHave('timetable');
    }

    public function getDepartmentAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->assigned_department : 'Undefined Department';
    }

    public function getAddressAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->city}, {$this->internship_agreements()->first()->country}" : 'Undefined Address';
    }

    public function getOrganizationNameAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->organization_name : 'Undefined Organization';
    }

    public function getExternalSupervisorNameAttribute()
    {
        return ucwords(strtolower($this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->encadrant_ext_name}" : 'Undefined External Supervisor'));
    }

    public function getExternalSupervisorAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->encadrant_ext_name}, {$this->internship_agreements()->first()->encadrant_ext_fonction}" : 'Undefined External Supervisor';
    }

    public function getExternalSupervisorContactAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->encadrant_ext_tel}, {$this->internship_agreements()->first()->encadrant_ext_mail}" : 'Undefined External Supervisor Contact';
    }

    public function getExternalSupervisorEmailAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->encadrant_ext_mail}" : 'Undefined External Supervisor Email';
    }

    public function getParrainAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->parrain_name}, {$this->internship_agreements()->first()->parrain_fonction}" : 'Undefined Parrain';
    }

    public function getParrainContactAttribute()
    {
        return $this->internship_agreements()->first() ? "{$this->internship_agreements()->first()->parrain_tel}, {$this->internship_agreements()->first()->parrain_mail}" : 'Undefined Parrain Contact';
    }

    public function getKeywordsAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->keywords : 'Undefined Keywords';
    }

    public function getDefensePlanAttribute()
    {
        return $this->timetable()->exists() ? "Le {$this->timetable->timeslot->end_time->format('d M Y')} de {$this->timetable->timeslot->start_time->format('H:i')} à {$this->timetable->timeslot->end_time->format('H:i')}, {$this->timetable->room->name}" : __('Undefined Defense Date');
    }

    public function getProjectDatesAttribute()
    {
        return "Du {$this->start_date->format('d M')} au {$this->end_date->format('d M Y')}";
    }

    public function getAdministrativeSupervisorAttribute()
    {
        $AdministrativeSupervisor = User::administrativeSupervisor($this->internship_agreements()->first()->student->program->value);
        // dd($AdministrativeSupervisor);

        return $AdministrativeSupervisor ? $AdministrativeSupervisor->full_name : 'Undefined Administrative Supervisor';
    }

    public function getAcademicSupervisorAttribute()
    {
        $AcademicSupervisor = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value)
            ->first();
        // dd($AcademicSupervisor);

        return $AcademicSupervisor ? $AcademicSupervisor->full_name : 'Undefined Academic Supervisor';
    }

    public function getReviewer1Attribute()
    {
        $Reviewer1 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer1->value)
            ->first();
        // dd($Reviewer1);

        return $Reviewer1 ? $Reviewer1->full_name : 'Undefined Reviewer 1';
    }

    public function getReviewer2Attribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer2->value)
            ->first();
        // dd($Reviewer2);

        return $Reviewer2 ? $Reviewer2->full_name : 'Undefined Reviewer 2';
    }

    public function defense_authorized_by_user()
    {
        return $this->belongsTo(User::class, 'defense_authorized_by', 'id');
    }

    public function authorizeDefense()
    {
        try {
            if (Gate::denies('authorize-defense', $this)) {
                throw new AuthorizationException();
            }

            $this->defense_authorized_at = now();
            $this->defense_authorized_by = auth()->id();
            $this->defense_status = Enums\DefenseStatus::Authorized;
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

    public function generateEvaluationSheet()
    {
        // we gonna use GenerateDefenseDocuments service and generateEvaluationSheet method to generate the evaluation sheet

        $service = new Services\GenerateDefenseDocuments();
        $service->generateEvaluationSheet($this);

        // event(new \App\Events\DefenseAuthorized($this));

    }

    // public function getEvaluationSheetUrlAttribute()
    // {
    //     return Storage::url("document/evaluation_sheet/{$this->id}.pdf");
    // }

    public function getOrganizationEvaluationSheetUrlAttribute()
    {
        if ($this->attributes['organization_evaluation_sheet_url']) {
            return Storage::url($this->attributes['organization_evaluation_sheet_url']);
        }

        return null;
    }
}
