<?php

namespace App\Filament\Administration\Pages;

class InternshipsDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'internshipsDashboard';

    protected static ?string $title = 'Final projects dashboard';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Final projects dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

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
            return false;

            return auth()->user()->isSuperAdministrator();
            //  || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();
        } else {
            return false;
        }
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Administration\Widgets\FinalInternshipsPerProgramChart::class,

            // \App\Filament\Administration\Widgets\AssignedSupervisorsChart::class,
            // \App\Filament\Administration\Widgets\InternshipsByEndDateChart::class,
            // \App\Filament\Administration\Widgets\InternshipsByEndDateDailyChart::class,
            // // \App\Filament\Administration\Widgets\InternshipsDistributionByDepartmentChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerCityChartFrance::class,
            // \App\Filament\Administration\Widgets\InternshipsPerCityChartMorocco::class,
            // \App\Filament\Administration\Widgets\InternshipsPerCountryChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerMonthChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerOrganizationChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerProgramChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerStatusChart::class,
            // \App\Filament\Administration\Widgets\InternshipsPerWeekChart::class,
            // \App\Filament\Administration\Widgets\InternshipsStatusChart::class,
            // // \App\Filament\Administration\Widgets\ProfessorsParticipationTable::class,
            // \App\Filament\Administration\Widgets\ProgressionTimelineChart::class,
            // // \App\Filament\Administration\Widgets\TimetableOverviewChart::class,
            // // \App\Filament\Administration\Widgets\EntrepriseContactsChart::class,
            // // \App\Filament\Administration\Widgets\DefensesCalendarWidget::class,
            // // \App\Filament\Administration\Widgets\AnouncementsCalendarWidget::class,
        ];
    }
}
