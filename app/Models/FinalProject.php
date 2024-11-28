<?php

namespace App\Models;

use App\Models\Traits\HasProfessors;
use App\Models\Traits\Projectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments; // Updated trait name

class FinalProject extends Model
{
    use HasFactory;
    use HasFilamentComments;

    // use HasProfessors;
    use Projectable;
    use SoftDeletes; // Updated trait

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
