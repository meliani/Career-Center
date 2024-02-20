<?php

namespace App\Policies;

use App\Models\User;

class ScheduleParametersPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole($this->administrators);
    }
}
