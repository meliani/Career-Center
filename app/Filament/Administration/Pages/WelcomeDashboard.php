<?php

namespace App\Filament\Administration\Pages;

class WelcomeDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'welcome-dashboard';

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

    protected function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\Administration\Widgets\Dashboards\YearlyTimelineWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Administration\Widgets\Dashboards\AdminGettingStartedWidget::class,
            \App\Filament\Administration\Widgets\Dashboards\YearlyTimelineWidget::class,
            \App\Filament\Administration\Widgets\FinalInternshipsPerProgramChart::class,

            // \App\Filament\Administration\Widgets\AssignedSupervisorsChart::class,
            // \App\Filament\Administration\Widgets\ProfessorsParticipationTable::class,
            // \App\Filament\Administration\Widgets\DefensesCalendarWidget::class,

            // \App\Filament\Administration\Widgets\ProgressionTimelineChart::class,
            // \App\Filament\Administration\Widgets\TimetableOverviewChart::class,
            // \App\Filament\Administration\Widgets\EntrepriseContactsChart::class,
            // \App\Filament\Administration\Widgets\AnouncementsCalendarWidget::class,
        ];
    }
}
