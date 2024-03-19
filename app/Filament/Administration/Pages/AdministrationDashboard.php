<?php

namespace App\Filament\Administration\Pages;

class AdministrationDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'administrationDashboard';

    protected static ?string $title = 'Administration dashboard';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __(static::$title);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator();
    }
}
