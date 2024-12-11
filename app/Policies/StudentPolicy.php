<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy extends CorePolicy
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
    public function view(User $user, Student $student): bool
    {

        if ($user->isAdministrator() || $user->isDirection()) {
            return true;
        } elseif (($user->isProfessor() && $student->projects->each(fn ($project, $key) => $project->professors->each(fn ($professor, $key) => $professor->id === $user->id)))) {
            return true;
        } elseif ($user->isProgramCoordinator() && $student->program === $user->assigned_program) {
            return true;

        } elseif ($user->isDepartmentHead() && $student->internship->assigned_department === $user->assigned_department) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->hasAnyRole($this->administrators);
    }
}
