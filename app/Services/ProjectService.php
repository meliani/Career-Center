<?php

namespace App\Services;

use App\Models\DefenseSchedule;
use App\Models\Internship;
use App\Models\Project;

class ProjectService{

    
}

// public function internshipContracts(): BelongsToMany
    // {
    //     return $this->belongsToMany(InternshipContract::class, 'project_internship_contract')
    //         ->withTimestamps()
    //         ->limit(2);
    // }
    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->withPivot('role')
    //         ->withTimestamps();
    // }

    // public function worker(): HasMany
    // {
    //     return $this->hasMany(User::class)->where('role', 'worker');
    // }

    // public function supervisors(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->wherePivot('role', ProjectRoleEnum::supervisor())
    //         ->using(ProjectUser::class);
    // }

    // public function hasMaxSupervisorsReached(): bool
    // {
    //     return $this->supervisors()->count() >= 2; // Adjust the maximum number of supervisors as needed
    // }
    // public function hasMaxWorkersReached(): bool
    // {
    //     return $this->workers()->count() >= 2; // Adjust the maximum number of supervisors as needed
    // }

    // public function workers(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->wherePivot('role', ProjectRoleEnum::worker()->value);
    // }

    // public function assignStudent(Student $student, array $attributes = []): void
    // {
    //     if ($this->hasMaxWorkersReached()) {
    //         throw new \Exception('Maximum number of workers reached');
    //     }

    //     $this->users()->attach(
    //         $student, $attributes ??
    //         [
    //             'role' => ProjectRoleEnum::worker(),
    //         ]
    //     );
    // }
    // public function assignProfessor(Professor $professor, array $attributes = []): void
    // {
    //     if ($this->hasMaxSupervisorsReached()) {
    //         throw new \Exception('Maximum number of supervisors reached');
    //     }
    //     $this->users()->attach(
    //         $professor, $attributes ??
    //         [
    //             'role' => ProjectRoleEnum::supervisor(),
    //         ]
    //     );
    // }