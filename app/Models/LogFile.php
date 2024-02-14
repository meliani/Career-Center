<?php

namespace App\Models;

use Rabol\FilamentLogviewer\Models;

class LogFile extends Models\LogFile
{
    public Static function canViewAny(): bool
    {
        return auth()->user()->isAdministrator();
    }
    public Static function canView(): bool
    {
        return auth()->user()->isAdministrator();
    }
    public Static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
}
