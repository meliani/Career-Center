<?php

namespace App\Policies;

use App\Models\FinalYearInternshipAgreement;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;

class FinalYearInternshipAgreementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User | Student $user): bool
    {
        // if student model should have access only to its final year internship agreements
        if ($user instanceof Student) {
            return $user->id === $finalYearInternshipAgreement->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User | Student $user, FinalYearInternshipAgreement $finalYearInternshipAgreement): bool
    {
        if ($user instanceof Student) {
            return $user->id === $finalYearInternshipAgreement->student_id;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User | Student $user): bool
    {
        if ($user instanceof Student) {
            $exists = FinalYearInternshipAgreement::where('student_id', $user->id)
                ->where('year_id', Year::current()->id)
                ->exists();

            return ! $exists;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User | Student $user, FinalYearInternshipAgreement $finalYearInternshipAgreement): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User | Student $user, FinalYearInternshipAgreement $finalYearInternshipAgreement): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User | Student $user, FinalYearInternshipAgreement $finalYearInternshipAgreement): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User | Student $user, FinalYearInternshipAgreement $finalYearInternshipAgreement): bool
    {
        return $user->isAdministrator();
    }
}