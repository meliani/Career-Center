<?php

namespace App\Policies;

use App\Models\Professor;
use App\Models\User;

class ProfessorPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isAdministrator() || $user->isDirection()) {
            return true;
        } elseif ($user->isProgramCoordinator()) {
            return true;
        } elseif ($user->isDepartmentHead()) {
            return true;
        } elseif ($user->isProfessor()) {
            return true;
        }

        return false;

    }

    public function view(User $user, Professor $professor)
    {
        if ($user->isDepartmentHead() && $professor->department === $user->department) {
            return $user->id === $professor->id;
            // return true;
        } elseif ($user->isProfessor() && $professor->projects->each(fn ($project, $key) => $project->professors->each(fn ($professor, $key) => $professor->id === $user->id))) {
            return $user->id === $professor->id;
            // return true;
        }

        return $user->isAdministrator() || $user->isDirection();
    }

    public function update(User $user, Professor $professor)
    {
        return $user->isAdministrator();
    }

    public function delete(User $user, Professor $professor)
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User $user, Professor $professor)
    {
        return $user->isAdministrator();
    }
}
