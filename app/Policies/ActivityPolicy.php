<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Role;


class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::SuperAdministrator);
    }
    public function view(User $user): bool
    {
        return $user->hasRole(Role::SuperAdministrator);
    }
}
