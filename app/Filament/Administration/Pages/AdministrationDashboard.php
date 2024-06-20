<?php

namespace App\Filament\Administration\Pages;

use JibayMcs\FilamentTour\Tour\HasTour;
use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;

class AdministrationDashboard extends \Filament\Pages\Dashboard
{
    use HasTour;

    protected static string $routePath = 'administrationDashboard';

    protected static ?string $title = 'Administration dashboard';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __(static::$title);
    }

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();
        } else {
            return false;
        }
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
