<?php

namespace App\Filament\Alumni\Pages;

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
            // \App\Filament\Administration\Widgets\DefensesPerProgramChart::class,
            // \App\Filament\Administration\Widgets\FirstYearApprenticeshipsPerProgramChart::class,
            // \App\Filament\Administration\Widgets\SecondYearApprenticeshipsPerProgramChart::class,

            \App\Filament\Alumni\Widgets\AccountPendingApprovalMessage::class,
            \App\Filament\Alumni\Widgets\EmailPendingVerificationMessage::class,
        ];
    }
}
