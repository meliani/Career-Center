<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternshipAgreement;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\CorePolicy;
use Illuminate\Support\Facades\Gate;


class ProfessorPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole($this->powerProfessors);
    }
}
