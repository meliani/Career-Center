<?php

namespace App\Filament\App\Pages;

class WelcomeDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'WelcomeDashboard';

    protected static ?string $title = 'Welcome';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Welcome dashboard';

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
            \App\Filament\App\Widgets\Dashboards\GettingStartedWidget::class,
            // \App\Filament\Widgets\DefensesPerProgramChart::class,
            \App\Filament\Widgets\FirstYearApprenticeshipsPerProgramChart::class,
            \App\Filament\Widgets\SecondYearApprenticeshipsPerProgramChart::class,
        ];
    }
}
