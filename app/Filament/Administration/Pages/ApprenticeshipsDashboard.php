<?php

namespace App\Filament\Administration\Pages;

class ApprenticeshipsDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'ApprenticeshipsDashboard';

    protected static ?string $title = 'Apprenticeships';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Apprenticeships dashboard';

    protected static ?string $navigationGroup = 'Dashboards';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    // public function getTitle(): string
    // {
    //     return __(static::$title) . ' ' . auth()->user()->long_full_name;
    // }

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return false;

            return auth()->user()->isSuperAdministrator();
            // || auth()->user()->isAdministrator()
            // || auth()->user()->isProfessor()
            // || auth()->user()->isDepartmentHead()
            // || auth()->user()->isProgramCoordinator()
            // || auth()->user()->isAdministrativeSupervisor();
        } else {
            return false;
        }
    }

    public function getTitle(): string
    {
        return __(static::$title);
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel);
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Administration\Widgets\FirstYearApprenticeshipsPerProgramChart::class,
            \App\Filament\Administration\Widgets\SecondYearApprenticeshipsPerProgramChart::class,
        ];
    }
}
