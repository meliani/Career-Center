<?php

namespace App\Filament\App\Pages;

use JibayMcs\FilamentTour\Tour\HasTour;
use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;

class WelcomeDashboard extends \Filament\Pages\Dashboard
{
    use HasTour;

    protected static string $routePath = 'WelcomeDashboard';

    protected static ?string $title = 'Welcome';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __(static::$title) . ' ' . auth()->user()->long_full_name;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public function tours(): array
    {
        return [
            Tour::make('dashboard')
                ->steps(
                    Step::make()
                        ->title('Welcome to Career Center Dashboard !')
                        ->description('This is your dashboard, you can see all the important information here.'),
                    Step::make('.fi-avatar')
                        ->title('Here is your avatar and your app notification !')
                        ->description('You can edit your profile information and check your app notification here.')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary'),
                ),
        ];
    }
}
