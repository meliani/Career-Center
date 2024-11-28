<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class FinalProject extends Model
{
    use HasFactory;
    use HasFilamentComments;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'language',
        'start_date',
        'end_date',
        'organization_evaluation_received_at',
        'organization_evaluation_received_by',
        'defense_status',
        'defense_authorized',
        'defense_authorized_by',
        'evaluation_sheet_url',
        'organization_evaluation_sheet_url',
        'organization_id',
        'external_supervisor_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'agreement_verified' => 'datetime',
        'supervisor_approved' => 'datetime',
        'organization_evaluation_received_at' => 'datetime',
        'defense_authorized' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_projects', 'project_id', 'professor_id')
            ->withPivot(
                'jury_role',
                'votes',
                'is_president',
                'was_present',
                'created_by',
                'updated_by',
                'approved_by',
                'supervision_status',
                'last_meeting_date',
                'next_meeting_date',
            )->withTimestamps()
            ->using(ProfessorProject::class);

    }

    public function timetable()
    {
        return $this->morphOne(Timetable::class, 'schedulable');
    }

    public function timetables()
    {
        return $this->morphOne(Timetable::class, 'schedulable');
    }

    public function internship_agreements()
    {
        return $this->hasMany(FinalYearInternshipAgreement::class, 'project_id', 'id');
    }

    public function final_year_internship_agreements()
    {
        return $this->hasMany(FinalYearInternshipAgreement::class, 'project_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function externalSupervisor()
    {
        return $this->belongsTo(FinalYearInternshipContact::class, 'external_supervisor_id');
    }
}
