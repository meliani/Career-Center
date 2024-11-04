<?php

namespace App\Policies;

use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Student;
use App\Models\User;

class InternshipAgreementPolicy extends CorePolicy
{
    public function viewAny(User | Student $user): bool
    {
        if ($user->isAdministrator() || $user->isDirection()) {
            // || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead()) {
            return true;
        }

        return false;
    }

    public function view(User | Student $user, InternshipAgreement | FinalYearInternshipAgreement $internship)
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

    public function update(User | Student $user, InternshipAgreement | FinalYearInternshipAgreement $internship)
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

    public function delete(User | Student $user, InternshipAgreement | FinalYearInternshipAgreement $internship)
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User | Student $user, InternshipAgreement | FinalYearInternshipAgreement $internship)
    {
        return $user->isAdministrator();
    }
}
