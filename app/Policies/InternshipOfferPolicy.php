<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\InternshipOffer;
use App\Models\Student;
use App\Models\User;

class InternshipOfferPolicy extends CorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User | Student $user): bool
    {
        if ($user instanceof Student) {
            return true;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User | Student $user, InternshipOffer $offer): bool
    {
        if ($user instanceof Student) {
            return true;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User | Student $user): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User | Student $user, InternshipOffer $offer): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User | Student $user, InternshipOffer $offer): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User | Student $user, InternshipOffer $offer): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User | Student $user, InternshipOffer $offer): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator);
    }
}
