<?php

namespace App\Policies;

use App\Models\InternshipAgreement;
use App\Models\User;

class InternshipAgreementPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isAdministrator() || $user->isDirection()) {
            // || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead()) {
            return true;
        }

        return false;
    }

    public function view(User $user, InternshipAgreement $internship)
    {
        if ($user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isDepartmentHead()) {
            return true;
        } elseif ($user->isProfessor() && $internship->project?->professors->each(fn ($professor, $key) => $professor->id === $user->id)) {
            return true;
        } elseif ($user->isProgramCoordinator() && $internship->project->students
            ->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            return true;
        }

        return false;
    }

    public function update(User $user, InternshipAgreement $internship)
    {
        if ($user->isAdministrator()) {
            return true;
        } elseif ($user->isProfessor() && $internship->project?->professors->each(fn ($professor, $key) => $professor->id === $user->id)) {
            return true;
        } elseif ($user->isProgramCoordinator() && $internship->project?->students
            ->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, InternshipAgreement $internship)
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User $user, InternshipAgreement $internship)
    {
        return $user->isAdministrator();
    }
}
