<?php

namespace App\Policies;

use App\Models\FinalYearInternshipAgreement;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;

class FinalYearInternshipAgreementPolicy extends CorePolicy
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
    public function view(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        if ($user instanceof Student) {
            return $user->id === $agreement->student_id;
        }

        return $user->isAdministrator() ||
               $user->isProgramCoordinator() ||
               $user->isDepartmentHead();
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

            return ! $exists && $user->level === \App\Enums\StudentLevel::ThirdYear;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        if ($user instanceof Student) {
            return $user->id === $agreement->student_id &&
                   $agreement->status === \App\Enums\Status::Draft;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can validate agreements.
     */
    public function validate(User | Student $user, ?FinalYearInternshipAgreement $agreement = null): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        if ($agreement && $user->isProgramCoordinator()) {
            return $agreement->student->program === $user->assigned_program;
        }

        return $user->isAdministrator() || $user->isProgramCoordinator();
    }

    /**
     * Determine whether the user can sign agreements.
     */
    public function sign(User | Student $user, ?FinalYearInternshipAgreement $agreement = null): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can achieve agreements.
     */
    public function achieve(User | Student $user, ?FinalYearInternshipAgreement $agreement = null): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can manage agreements.
     */
    public function manage(User | Student $user): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator() ||
               $user->isProgramCoordinator() ||
               $user->isDepartmentHead();
    }

    /**
     * Determine whether the user can assign agreements to projects.
     */
    public function assignToProject(User | Student $user): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can receive agreements.
     */
    public function receive(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can assign department to agreements.
     */
    public function assignDepartment(User | Student $user, FinalYearInternshipAgreement $agreement): bool
    {
        if ($user instanceof Student) {
            return false;
        }

        if ($user->isProgramCoordinator()) {
            return $agreement->student->program === $user->assigned_program;
        }

        return $user->isAdministrator();
    }
}
