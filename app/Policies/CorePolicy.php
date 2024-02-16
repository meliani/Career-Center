<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CorePolicy
{
    use HandlesAuthorization;

    protected $administrators = [Role::SuperAdministrator, Role::Administrator];

    protected $professors = [Role::Professor, Role::DepartmentHead, Role::ProgramCoordinator];

    protected $powerProfessors = [Role::SuperAdministrator, Role::Administrator, Role::ProgramCoordinator, Role::Direction];

    protected $direction = [Role::Direction];

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    // public function view(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    public function create(User $user): bool
    {
        return $user->hasAnyRole($this->administrators);
    }

    // public function update(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function delete(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function restore(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function forceDelete(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function viewSome(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function viewRelated(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }

    // public function updateCertain(User $user, ?Model $model): bool
    // {
    //     return $user->hasAnyRole($this->administrators);
    // }
}
