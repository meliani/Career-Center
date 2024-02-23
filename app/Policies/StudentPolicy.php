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
        return $user->hasAnyRole($this->administrators);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {

        return $user->isAdministrator() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDirection();
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
