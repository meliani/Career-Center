<?php

namespace App\Models\Traits;

use App\Enums;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

trait ProjectAttributes
{
    public function getIdPfeAttribute()
    {
        return $this->agreements->map(function ($agreement) {
            return $agreement->agreeable->student->id_pfe ?? '';
        })->filter()->implode(' & ');
    }

    public function getStudentsCollection()
    {
        return $this->agreements->map(function ($agreement) {
            return $agreement->agreeable?->student;
        })->unique();

    }

    public function getStudentsAttribute()
    {
        return $this->agreements->map(function ($agreement) {
            return $agreement->agreeable?->student;
        })->filter()->unique();
    }

    public function getOrganization()
    {
        return $$this->agreements->map(function ($agreement) {
            return $agreement->agreeable->organization;
        })->unique();
    }

    public function getStudentsNamesAttribute()
    {
        return $this->agreements()
            // ->where('agreeable_type', InternshipAgreement::class)
            ->with('agreeable.student')
            ->get()
            ->pluck('agreeable.student')
            ->unique()->implode('name', ' & ');
    }

    public function getDescriptionAttribute()
    {
        $agreement = $this->agreements()->with('agreeable')->first();

        return $agreement ? $agreement->agreeable->description : 'Undefined Description';
    }

    public function getStudentsProgramsAttribute()
    {
        return $this->agreements()
            ->with('agreeable.student')
            ->get()
            ->pluck('agreeable.student.program')
            ->unique()
            ->map->getLabel()
            ->implode(' & ');
    }

    public function getOrganizationNameAttribute()
    {
        return $this->agreements->map(function ($agreement) {
            return $agreement->agreeable->organization->name;
        })->unique()->first() ?? 'Undefined Organization';
    }

    public function getDefensePlanAttribute()
    {
        if (! $this->timetable()->exists()) {
            return __('Undefined Defense Date');
        }

        return __(':date from :start_time to :end_time, :room', [
            'date' => $this->timetable->timeslot->end_time->format('d M Y'),
            'start_time' => $this->timetable->timeslot->start_time->format('H:i'),
            'end_time' => $this->timetable->timeslot->end_time->format('H:i'),
            'room' => $this->timetable->room->name
        ]);
    }

    public function getProjectDatesAttribute()
    {
        return "Du {$this->start_date->format('d M')} au {$this->end_date->format('d M Y')}";
    }

    public function getAdministrativeSupervisorAttribute()
    {
        $firstStudent = $this->agreements->map(function ($agreement) {
            return $agreement->agreeable?->student;
        })->filter()->first();
        
        if (!$firstStudent) {
            return 'Undefined Administrative Supervisor';
        }
        
        $AdministrativeSupervisor = User::administrativeSupervisor($firstStudent->program->value);

        return $AdministrativeSupervisor ? $AdministrativeSupervisor->full_name : 'Undefined Administrative Supervisor';
    }

    public function getAdministrativeSupervisorUserAttribute()
    {
        $firstStudent = $this->agreements->map(function ($agreement) {
            return $agreement->agreeable?->student;
        })->filter()->first();
        
        if (!$firstStudent) {
            return null;
        }
        
        return User::administrativeSupervisor($firstStudent->program->value);
    }

    public function getAcademicSupervisorNameAttribute()
    {
        $AcademicSupervisor = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value)
            ->first();

        return $AcademicSupervisor ? $AcademicSupervisor->full_name : __('Undefined Academic Supervisor');
    }

    public function getExternalSupervisorNameAttribute()
    {
        $externalSupervisor = $this->agreements->map(function ($agreement) {
            return $agreement->agreeable->externalSupervisor;
        })->filter()->first();

        return $externalSupervisor ? $externalSupervisor->full_name : __('Undefined External Supervisor');
    }

    public function getExternalSupervisorEmailAttribute()
    {
        $externalSupervisor = $this->agreements->map(function ($agreement) {
            return $agreement->agreeable->externalSupervisor;
        })->filter()->first();

        return $externalSupervisor ? $externalSupervisor->email : null;
    }

    public function getAcademicSupervisorPresenceAttribute()
    {
        $AcademicSupervisor = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor)
            ->wherePivot('was_present', true)
            ->first();

        return $AcademicSupervisor ? '☀️' : '🌕';
    }

    public function getReviewer1Attribute()
    {
        $Reviewer1 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::FirstReviewer->value)
            ->first();

        return $Reviewer1 ? $Reviewer1->full_name : __('Undefined First Reviewer');
    }

    public function getReviewer1PresenceAttribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)
            ->wherePivot('was_present', true)
            ->first();

        return $Reviewer2 ? '☀️' : '🌕';
    }

    public function getReviewer2Attribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::SecondReviewer->value)
            ->first();

        return $Reviewer2 ? $Reviewer2->full_name : __('Undefined Second Reviewer');
    }

    public function getReviewer2PresenceAttribute()
    {
        $Reviewer2 = $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)
            ->wherePivot('was_present', true)
            ->first();

        return $Reviewer2 ? '☀️' : '🌕';
    }

    public function getEvaluationSheetUrlAttribute()
    {
        if ($this->attributes['evaluation_sheet_url']) {
            return $this->attributes['evaluation_sheet_url'];
        }
        // return Storage::url("document/evaluation_sheet/{$this->id}.pdf");

        // return __('Not Generated Yet');

    }

    public function getOrganizationEvaluationSheetUrlAttribute()
    {
        if ($this->attributes['organization_evaluation_sheet_url']) {
            return Storage::url($this->attributes['organization_evaluation_sheet_url']);
        }

        // return __('Not Uploaded Yet');
    }

    /**
     * Get the assigned departments for the project.
     *
     * @return string
     */
    public function getAssignedDepartmentsAttribute()
    {
        return $this->agreements->map(function ($agreement) {
            return $agreement->agreeable->assigned_department?->getLabel();
        })->unique()->implode(' & ');
    }
}
