<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ProjectStudent extends MorphPivot
{
    protected $table = 'project_student';

    protected $guarded = [];

    // public function agreement()
    // {
    //     return $this->morphOne(FinalYearInternshipAgreement::class, 'agreement');
    // }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function project()
    {
        return $this->morphTo();
    }
}
