<?php

namespace App\Policies;

use App\Enums;
use App\Models\InternshipAgreement;
use App\Models\User;

class InternshipAgreementPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasAnyRole(Enums\Role::getAll())) {
            return true;
        }

        return false;
    }

    public function view(User $user, InternshipAgreement $internship)
    {
        if ($user->isAdministrator()) {
            return true;
            // } elseif ($user->isProfessor() && $internship->project->professors->each(fn ($professor, $key) => $professor->id === $user->id)) {
        } elseif ($user->isProfessor() && $internship->project?->professors === $user->id) {
            return true;
        } elseif ($user->isProgramCoordinator() && $internship->project?->students->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            return true;
        } elseif ($user->isDirection()) {
            return true;
        }

        return false;
    }

    public function update(User $user, InternshipAgreement $internship)
    {
        if ($user->isAdministrator()) {
            return true;
        } elseif ($user->isProfessor() && $internship->project?->professors === $user->id) {
            return true;
        } elseif ($user->isProgramCoordinator() && $internship->project?->students->each(fn ($student, $key) => $student->program === $user->assigned_program)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, InternshipAgreement $internship)
    {
        return $user->hasAnyRole($this->administrators);
    }

    public function forceDelete(User $user, InternshipAgreement $internship)
    {
        return $user->hasAnyRole($this->administrators);
    }
}
