<?php

namespace App\Models\Traits;

use App\Models\ProjectStudent;
use App\Models\Student;

trait HasStudents
{
    public function students()
    {
        return $this->morphToMany(Student::class, 'project', 'project_student', 'project_id', 'student_id')
            ->withPivot('id')
            ->using(ProjectStudent::class)
            ->withTimestamps();
    }

    public function hasTeammate()
    {
        return $this->students()->count() > 1;
    }
}
