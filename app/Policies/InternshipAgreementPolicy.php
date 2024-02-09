<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternshipAgreement;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\CorePolicy;
use Illuminate\Support\Facades\Gate;


class InternshipAgreementPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        // Check if the user is an administrator
        // if (! Gate::allows('view-internship', $internship)) {
        //     return true;
        // }
        if ($user->hasAnyRole($this->administrators)){
            return true;
        }
        return false;
    }

    public function view(User $user, InternshipAgreement $internship)
    {
        if ($user->hasAnyRole($this->powerProfessors)) {
            return true;
        }
    }
    public function update(User $user, InternshipAgreement $internship)
    {
        return $user->hasAnyRole($this->powerProfessors);
    }

    public function delete(User $user, InternshipAgreement $internship)
    {
        return $user->hasAnyRole($this->administrators);
    }

    public function viewSome(User $user, InternshipAgreement $internship)
    {
        if ($user->hasAnyRole($this->powerProfessors) && $internship->student->program === $user->program_coordinator)
        {
            return true;
        }
    }

    public function viewRelated(User $user, InternshipAgreement $internship)
    {
        if ($user->hasAnyRole($this->powerProfessors) && $internship->student->program === $user->program_coordinator) {
            return true;
        }
    }

    public function updateCertain(User $user, InternshipAgreement $internship)
    {
        return $user->hasAnyRole($this->powerProfessors) && $user->program_coordinator === $internship->student->program;
    }
}
