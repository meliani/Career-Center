<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Rabol\FilamentLogviewer\Models\LogFile;

class LogFilePolicy extends CorePolicy
{
    // use HandlesAuthorization;

    public static function canView(): bool
    {
        return false;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, LogFile $logFile)
    {
        return false;
    }

    public function create(User $user, LogFile $logFile)
    {
        return false;
    }

    public function update(User $user, LogFile $logFile)
    {
        return false;
    }

    public function delete(User $user, LogFile $logFile)
    {
        return false;
    }

    public function deleteAny(User $user)
    {
        return false;
    }
}
