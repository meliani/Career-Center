<?php

namespace App\Filament\Administration\Pages;

class AdministrationDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'administrationDashboard';

    protected static ?string $title = 'Welcome dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Welcome dashboard';

    protected static ?string $navigationGroup = 'Dashboards';

    public function getTitle(): string
    {
        return __(static::$title);
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel);
    }

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();
        } else {
            return false;
        }
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Administration\Widgets\Dashboards\GettingStartedWidget::class,

            // \App\Filament\Administration\Widgets\AssignedSupervisorsChart::class,
            // \App\Filament\Administration\Widgets\ProfessorsParticipationTable::class,
            // \App\Filament\Administration\Widgets\DefensesCalendarWidget::class,

            // \App\Filament\Administration\Widgets\ProgressionTimelineChart::class,
            // \App\Filament\Administration\Widgets\TimetableOverviewChart::class,
            // \App\Filament\Administration\Widgets\EntrepriseContactsChart::class,
            // \App\Filament\Administration\Widgets\AnouncementsCalendarWidget::class,
        ];
    }

    // public function tours(): array
    // {
    //     return [
    //         \JibayMcs\FilamentTour\Tour\Tour::make('dashboard')
    //             ->steps(
    //                 \JibayMcs\FilamentTour\Tour\Step::class
    //                     ->title('Welcome to Career Center Dashboard !')
    //                     ->description('This is your dashboard, you can see all the important information here.'),
    //                 \JibayMcs\FilamentTour\Tour\Step::make('.fi-avatar')
    //                     ->title('Here is your avatar and your app notification !')
    //                     ->description('You can edit your profile information and check your app notification here.')
    //                     ->icon('heroicon-o-user-circle')
    //                     ->iconColor('primary'),
    //             ),
    //     ];
    // }
}
