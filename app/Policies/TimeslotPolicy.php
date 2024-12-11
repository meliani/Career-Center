<?php

namespace App\Policies;

use App\Models\InternshipAgreement;
use App\Models\User;

class TimeslotPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();
    }

    public function view(User $user, InternshipAgreement $internshipAgreement): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator();
    }

    public function update(User $user, InternshipAgreement $internshipAgreement): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator();
    }

    public function delete(User $user, InternshipAgreement $internshipAgreement): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator();
    }

    public function restore(User $user, InternshipAgreement $internshipAgreement): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator();
    }

    public function forceDelete(User $user, InternshipAgreement $internshipAgreement): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProgramCoordinator();
    }
}
