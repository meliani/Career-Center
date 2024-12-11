<?php

namespace App\Policies;

use App\Enums;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProjectPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        // Check if the user is an administrator
        // if (! Gate::allows('view-internship', $project)) {
        //     return true;
        // }
        if ($user->hasAnyRole(Enums\Role::getAll())) {
            return true;
        }

        return false;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {
            return true;
        } elseif ($user->isProfessor() && $project->professors->each(fn ($professor, $key) => $professor->id === $user->id)) {
            return true;
        } elseif ($user->isProgramCoordinator() && $project->students->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            return true;
        } elseif ($user->isDirection()) {
            return true;
        } elseif ($user->isDepartmentHead() && $project->students->each(fn ($student, $key) => $student->department === $user->department)) {
            return true;
        }

        return $project->agreements->contains('student_id', $user->id);
        foreach ($project->agreements as $agreement) {
            if ($agreement->student_id === $user->id) {
                return true;
            }
            if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {
                return true;
            } elseif ($user->isProfessor() && $project->professors === $user->id) {
                return false;
            } elseif ($user->isProgramCoordinator() && $project->students->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            }
        }
    }

    public function update(User $user, Project $project)
    {
        // Administrators and Administrative Supervisors always have access
        if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {

            return true;
        }

        // Program Coordinators can update projects for their assigned program
        if ($user->isProgramCoordinator()) {

            return $project
                ->whereHas('agreements', function ($query) use ($user) {
                    $query->whereMorphRelation(
                        'agreeable',
                        [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                        function ($query) use ($user) {
                            $query->whereHas('student', function ($query) use ($user) {
                                $query->where('program', $user->assigned_program->value);
                            });
                        }
                    );
                })
                ->exists();
        }

        // Department Heads can update projects in their department
        if ($user->isDepartmentHead()) {
            return $project
                ->whereHas('agreements', function ($query) use ($user) {
                    $query->whereMorphRelation(
                        'agreeable',
                        [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                        function ($query) use ($user) {
                            $query->where('assigned_department', $user->department->value);
                        }
                    );
                })
                ->exists();
        }
        // Professor can update their own projects
        if ($user->isProfessor()) {
            return $project->professors->contains($user->id);
        }

        return false;
    }

    public function delete(User $user, Project $project)
    {
        return $user->hasAnyRole($this->administrators);
    }

    public function create(User | Student $user): bool
    {

        return $user->isAdministrator();
    }
    // public function viewSome(User $user, Project $project)
    // {
    //     if ($user->hasAnyRole($this->professors) && $project->student->program === $user->assigned_program) {
    //         return true;
    //     }
    // }

    // public function viewRelated(User $user, Project $project)
    // {
    //     return false;
    // }

    // public function createRelated(User $user, Project $project)
    // {
    //     return false;
    // }

    // public function updateCertain(User $user, Project $project)
    // {
    //     return $user->hasAnyRole($this->powerProfessors) && $user->assigned_program === $project->student->program;
    // }
}
