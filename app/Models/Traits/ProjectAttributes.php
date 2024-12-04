<?php

namespace App\Models\Traits;

use App\Enums;
use Illuminate\Support\Facades\Storage;

trait ProjectAttributes
{
    public function getIdPfeAttribute()
    {
        if ($this->hasTeammate()) {
            return ($this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID') . ' ' . __('&') . ' ' . ($this->internship_agreements()->latest()->first() ? $this->internship_agreements()->latest()->first()->id_pfe : 'Undefined ID');
        } else {
            return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID';
        }
    }

    /**
     * Get all students names associated with this project through internship agreements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getStudentsNamesAttribute()
    {
        // get students names from each agreement's student
        // dd($this->agreements()
        //     ->where('agreeable_type', InternshipAgreement::class)
        //     ->with('agreeable.student')
        //     ->get()
        //     ->pluck('agreeable.student')
        //     ->unique());

        return $this->agreements()
            // ->where('agreeable_type', InternshipAgreement::class)
            ->with('agreeable.student')
            ->get()
            ->pluck('agreeable.student')
            ->unique()->implode('name', ' & ');
    }

    public function getStudentsProgramsAttribute()
    {
        return $this->students()->implode('program', ' & ');
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

    public function getDepartmentAttribute()
    {
        return $this->internship_agreements()->first()?->assigned_department ?? 'Undefined Department';
    }

    public function getAddressAttribute()
    {
        $agreement = $this->internship_agreements()->first();

        return $agreement ? "{$agreement->city}, {$agreement->country}" : 'Undefined Address';
    }

    public function getOrganizationNameAttribute()
    {
        // return $this->agreements->map(function ($agreement) {
        //     $agreeable = $agreement->agreeable;

        //     return match (get_class($agreeable)) {
        //         InternshipAgreement::class => $agreeable->organization_name,
        //         FinalYearInternshipAgreement::class => $agreeable->organization?->name,
        //         default => null
        //     };
        // })->filter()->first() ?? 'Undefined Organization';

        return $this->internship_agreements()->first()?->organization_name ?? 'Undefined Organization';
    }

    public function getExternalSupervisorNameAttribute()
    {
        // return $this->agreements->map(function ($agreement) {
        //     $agreeable = $agreement->agreeable;

        //     return match (get_class($agreeable)) {
        //         InternshipAgreement::class => $agreeable->encadrant_ext_name,
        //         FinalYearInternshipAgreement::class => $agreeable->externalSupervisor?->name,
        //         default => null
        //     };
        // })->filter()->first() ?? 'Undefined External Supervisor';

        return ucwords(strtolower($this->internship_agreements()->first()?->encadrant_ext_name ?? 'Undefined External Supervisor'));
    }

    public function getExternalSupervisorAttribute()
    {
        $agreement = $this->internship_agreements()->first();

        return $agreement ? "{$agreement->encadrant_ext_name}, {$agreement->encadrant_ext_fonction}" : 'Undefined External Supervisor';
    }

    public function getExternalSupervisorContactAttribute()
    {
        $agreement = $this->internship_agreements()->first();

        return $agreement ? "{$agreement->encadrant_ext_tel}, {$agreement->encadrant_ext_mail}" : 'Undefined External Supervisor Contact';
    }

    public function getExternalSupervisorEmailAttribute()
    {
        return $this->internship_agreements()->first()?->encadrant_ext_mail ?? 'Undefined External Supervisor Email';
    }

    public function getParrainAttribute()
    {
        // return $this->agreements->map(function ($agreement) {
        //     $agreeable = $agreement->agreeable;

        //     return match (get_class($agreeable)) {
        //         InternshipAgreement::class => $agreeable->parrain_name ?
        //             "{$agreeable->parrain_name}, {$agreeable->parrain_fonction}" : null,
        //         FinalYearInternshipAgreement::class => $agreeable->parrain ?
        //             "{$agreeable->parrain->name}, {$agreeable->parrain->position}" : null,
        //         default => null
        //     };
        // })->filter()->first() ?? 'Undefined Parrain';

        $agreement = $this->internship_agreements()->first();

        return $agreement ? "{$agreement->parrain_name}, {$agreement->parrain_fonction}" : 'Undefined Parrain';
    }

    public function getParrainContactAttribute()
    {
        $agreement = $this->internship_agreements()->first();

        return $agreement ? "{$agreement->parrain_tel}, {$agreement->parrain_mail}" : 'Undefined Parrain Contact';
    }

    public function getKeywordsAttribute()
    {
        return $this->internship_agreements()->first()?->keywords ?? 'Undefined Keywords';
    }

    public function getDefensePlanAttribute()
    {
        if (! $this->timetable()->exists()) {
            return __('Undefined Defense Date');
        }

        return "{$this->timetable->timeslot->end_time->format('d M Y')} de {$this->timetable->timeslot->start_time->format('H:i')} à {$this->timetable->timeslot->end_time->format('H:i')}, {$this->timetable->room->name}";
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

    public function getAcademicSupervisorPresenceAttribute()
    {
        $AcademicSupervisor = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor)
            ->wherePivot('was_present', true)
            ->first();
        // dd($AcademicSupervisor);

        return $AcademicSupervisor ? '☀️' : '🌕';
    }

    public function getReviewer1Attribute()
    {
        $Reviewer1 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer1->value)
            ->first();
        // dd($Reviewer1);

        return $Reviewer1 ? $Reviewer1->full_name : 'Undefined Reviewer 1';
    }

    public function getReviewer1PresenceAttribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer1)
            ->wherePivot('was_present', true)
            ->first();
        // dd($Reviewer2);

        return $Reviewer2 ? '☀️' : '🌕';
    }

    public function getReviewer2Attribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer2->value)
            ->first();
        // dd($Reviewer2);

        return $Reviewer2 ? $Reviewer2->full_name : 'Undefined Reviewer 2';
    }

    public function getReviewer2PresenceAttribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer2)
            ->wherePivot('was_present', true)
            ->first();
        // dd($Reviewer2);

        return $Reviewer2 ? '☀️' : '🌕';
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
