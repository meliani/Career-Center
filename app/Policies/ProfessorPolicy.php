<?php

namespace App\Policies;

use App\Models\Professor;
use App\Models\User;

class ProfessorPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isDirection() || $user->isProfessor();
    }

    public function view(User $user, Professor $professor)
    {
        if ($user->isDepartmentHead() && $professor->department === $user->department) {
            return $user->id === $professor->id;
        }

        return $user->isAdministrator() || $user->isProgramCoordinator() || $user->isDepartmentHead() || $user->isDirection() || $user->isProfessor();
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
