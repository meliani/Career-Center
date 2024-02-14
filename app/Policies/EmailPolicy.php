<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Role;
use App\Models\Email;


class EmailPolicy
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
    public static function canAccess(): bool
    {
        return false;
    }
}
