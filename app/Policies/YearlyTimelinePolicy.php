<?php

namespace App\Policies;

use App\Models\User;
use App\Models\YearlyTimeline;

class YearlyTimelinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isProfessor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, YearlyTimeline $yearlyTimeline): bool
    {
        return $user->isAdministrator() || $user->isProfessor();
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
    public function update(User $user, YearlyTimeline $yearlyTimeline): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, YearlyTimeline $yearlyTimeline): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, YearlyTimeline $yearlyTimeline): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, YearlyTimeline $yearlyTimeline): bool
    {
        return $user->isAdministrator();
    }
}
