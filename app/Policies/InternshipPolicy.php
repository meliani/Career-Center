<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Internship;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\CorePolicy;
use Illuminate\Support\Facades\Gate;

class InternshipPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        // Check if the user is an administrator
        // if (! Gate::allows('view-internship', $internship)) {
        //     return true;
        // }
        if ($user->hasAnyRole($this->professors)){
            return true;
        }
        return false;
    }

    public function view(User $user, Internship $internship)
    {
        // Check if the user is a professor and the internship's student's program matches the user's program
        if ($user->hasAnyRole($this->powerProfessors)) {
            return true;
        }
    }
    /**
     * Review function to be executed only by SuperAdministrator, Administrator, or ProgramCoordinator
     */
    // public function review(User $user, Internship $internship)
    // {
    //     if ($user->hasAnyRole($this->powerProfessors) && $internship->student->filiere_text === "SUD") {
    //         return true;
    //     }
    // }

    public function update(User $user, Internship $internship)
    {
        return $user->hasAnyRole($this->powerProfessors);
    }

    public function delete(User $user, Internship $internship)
    {
        return $user->hasRole('SuperAdministrator');
    }

    public function viewSome(User $user, Internship $internship)
    {
        if ($user->hasAnyRole($this->powerProfessors) && $internship->student->filiere_text === "SUD") //$user->program_coordinator)
        {
            return true;
        }
    }

    public function viewRelated(User $user, Internship $internship)
    {
        if ($user->hasAnyRole($this->powerProfessors) && $internship->student->filiere_text === "SUD") {
            return true;
        }
    }

    public function updateCertain(User $user, Internship $internship)
    {
        return $user->hasRole($this->powerProfessors) && $user->program_coordinator == $internship->student->filiere_text;
    }
}
