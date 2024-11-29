<?php

namespace App\Models\Traits;

use App\Models\ProjectStudent;
use App\Models\Student;

trait HasInternshipAgreements
{
    public function internship_agreements()
    {
        return $this->HasManyThrough(Student::class, ProjectStudent::class, 'project_id', 'id', 'id', 'student_id');
    }

    public function final_year_internship_agreements()
    {
        return $this->HasManyThrough(Student::class, ProjectStudent::class, 'project_id', 'id', 'id', 'student_id');
    }
}
