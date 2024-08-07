<?php

namespace App\Filament\Administration\Pages;

class ApprenticeshipsDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'ApprenticeshipsDashboard';

    protected static ?string $title = 'Apprenticeships';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Apprenticeships dashboard';

    // icon

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    // public function getTitle(): string
    // {
    //     return __(static::$title) . ' ' . auth()->user()->long_full_name;
    // }

    public static function canAccess(): bool
    {
        return true;
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
            \App\Filament\Widgets\FirstYearApprenticeshipsPerProgramChart::class,
            \App\Filament\Widgets\SecondYearApprenticeshipsPerProgramChart::class,
        ];
    }
}
