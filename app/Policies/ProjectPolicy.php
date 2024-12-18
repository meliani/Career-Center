<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Student;
use App\Models\User;

class ProjectPolicy extends CorePolicy
{
    public function viewAny(User | Student $user): bool
    {
        if ($user instanceof Student) {
            return true;

        }

        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isAdministrativeSupervisor();

    }

    public function view(User | Student $user, Project $project): bool
    {
        if ($user instanceof Student) {
            return true;
        }

        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isAdministrativeSupervisor();
    }

    public function update(User $user, Project $project)
    {
        if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {
            return true;
        }

        // Program Coordinators can update projects having students with same assigned program
        if ($user->isProgramCoordinator()) {

            return $project->agreements->some(function ($agreement) use ($user) {
                return $agreement->agreeable->student->program === $user->assigned_program;
            });
        }

        // Department Heads can update projects with internships in their department
        if ($user->isDepartmentHead()) {
            return $project
                ->agreements
                ->some(function ($agreement) use ($user) {
                    return $agreement->agreeable->assigned_department === $user->department;
                });
        }
        // Professor can update their own projects
        if ($user->isProfessor()) {
            // return $project->professors->contains($user->id);
            return false;
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

    public function sendDefenseEmail(User $user, Project $project)
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
