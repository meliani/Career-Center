<?php

namespace App\Policies;

use App\Enums;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProjectPolicy extends CorePolicy
{
    public function viewAny(User $user): bool
    {
        // Check if the user is an administrator
        // if (! Gate::allows('view-internship', $project)) {
        //     return true;
        // }
        if ($user->hasAnyRole(Enums\Role::getAll())) {
            return true;
        }

        return false;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasAnyRole($this->professors)) {
            return true;
        }
    }

    public function update(User $user, Project $project)
    {
        return $user->hasAnyRole($this->powerProfessors);
    }

    public function delete(User $user, Project $project)
    {
        return $user->hasAnyRole($this->administrators);
    }

    public function viewSome(User $user, Project $project)
    {
        if ($user->hasAnyRole($this->professors) && $project->student->program === $user->program_coordinator) {
            return true;
        }
    }

    public function viewRelated(User $user, Project $project)
    {
        if ($user->hasAnyRole($this->professors) && $project->student->program === $user->program_coordinator) {
            return true;
        }
    }

    public function updateCertain(User $user, Project $project)
    {
        return $user->hasAnyRole($this->powerProfessors) && $user->program_coordinator === $project->student->program;
    }
}
