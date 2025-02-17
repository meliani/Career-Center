<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ProjectAgreement extends MorphPivot
{
    protected $table = 'project_agreements';

    protected $fillable = [
        'project_id',
        'agreeable_id',
        'agreeable_type',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the parent agreeable model (agreement).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|\App\Models\Agreement
     */
    public function agreeable()
    {
        return $this->morphTo()->with('student');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'agreeable_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'agreeable_id');
    }

    public function parrain()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'parrain_id', 'id');
    }

    public function externalSupervisor()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'external_supervisor_id', 'id');
    }
}
