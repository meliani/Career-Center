<?php

namespace App\Filament\Administration\Pages;

class AdministrationDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'administrationDashboard';

    protected static ?string $title = 'Administration dashboard';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->isSuperAdministrator();
    }
}
