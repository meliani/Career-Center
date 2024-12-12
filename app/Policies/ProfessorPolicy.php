<?php

namespace App\Policies;

use App\Models\Professor;
use App\Models\User;

class ProfessorPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();

    }

    public function view(User $user, Professor $professor)
    {
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

    public function create(User | Student $user): bool
    {

        return $user->isAdministrator();
    }

    public function restore(User $user, Professor $professor)
    {
        return $user->isAdministrator();
    }

    public function createRelated(User $user, Professor $professor)
    {
        return $user->isAdministrator();
    }
}
