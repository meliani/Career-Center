<?php

namespace App\Policies;

use App\Models\Timetable;
use App\Models\User;

class TimetablePolicy extends CorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isProfessor() || $user->isAdministrativeSupervisor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timetable $timetable): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isProfessor() || $user->isAdministrativeSupervisor();
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
    public function update(User $user, Timetable $timetable): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isAdministrativeSupervisor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timetable $timetable): bool
    {
        return $user->isAdministrator();
    }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Timetable $timetable): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Timetable $timetable): bool
    // {
    //     //
    // }
}
