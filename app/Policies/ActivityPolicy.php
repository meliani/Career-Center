<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy extends CorePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User | Student $user): bool
    {
        return $user->hasRole(Role::Administrator);
    }

    public function view(User | Student $user): bool
    {
        return $user->hasRole(Role::Administrator);
    }
}
