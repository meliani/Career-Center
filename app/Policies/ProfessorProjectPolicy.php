<?php

namespace App\Policies;

use App\Models\ProfessorProject;
use App\Models\User;

class ProfessorProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProfessorProject $professorProject): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProfessorProject $professorProject): bool
    {
        if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {
            return true;
        }

        // Program Coordinators can update projects having students with same assigned program
        if ($user->isProgramCoordinator()) {

            return false;
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

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProfessorProject $professorProject): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProfessorProject $professorProject): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProfessorProject $professorProject): bool
    {
        //
    }
}
