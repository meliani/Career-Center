<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Rabol\FilamentLogviewer\Models\LogFile;
use App\Policies\CorePolicy;

class LogFilePolicy extends CorePolicy
{
    // use HandlesAuthorization;

    // public function viewAny(User $user): bool
    // {
    //     return false;
    // }
    // public function view(User $user, LogFile $logFile)
    // {
    //     return true;
    // }


    // public function create(User $user, LogFile $logFile)
    // {
    //     return true;
    // }

    // public function update(User $user, LogFile $logFile)
    // {
    //     return true;
    // }

    // public function delete(User $user, LogFile $logFile)
    // {
    //     return true;
    // }

    // public function deleteAny(User $user)
    // {
    //     return true;
    // }
}
