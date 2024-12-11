<?php

namespace App\Policies;

use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->isDirection() || $user->isProfessor() || $user->isProgramCoordinator() || $user->isDepartmentHead();
    }
}
