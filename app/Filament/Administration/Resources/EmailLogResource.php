<?php

namespace App\Filament\Administration\Resources;


use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class EmailLogResource extends EmailResource
{
    
    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdministrator();
    }
}
