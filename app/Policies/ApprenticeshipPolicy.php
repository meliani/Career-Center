<?php

namespace App\Policies;

use App\Models\Apprenticeship;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;

class ApprenticeshipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User | Student $user): bool
    {
        // if student model should have access only to its apprenticeships
        if ($user instanceof Student) {
            return $user->id === $apprenticeship->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User | Student $user, Apprenticeship $apprenticeship): bool
    {
        // if student model should have access only to his apprenticeships
        if ($user instanceof Student) {
            return $user->id === $apprenticeship->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User | Student $user): bool
    {
        if ($user instanceof Student) {
            $exists = Apprenticeship::where('student_id', $user->id)
                ->where('year_id', Year::current()->id)
                ->exists();

            return ! $exists;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User | Student $user, Apprenticeship $apprenticeship): bool
    {
        if ($user instanceof Student) {
            return $user->id === $apprenticeship->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User | Student $user, Apprenticeship $apprenticeship): bool
    {
        if ($user instanceof Student) {
            return $user->id === $apprenticeship->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User | Student $user, Apprenticeship $apprenticeship): bool
    {
        return $user->isAdministrator();

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User | Student $user, Apprenticeship $apprenticeship): bool
    {
        return $user->isAdministrator();

    }
}
