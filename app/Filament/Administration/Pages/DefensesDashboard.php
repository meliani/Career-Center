<?php

namespace App\Filament\Administration\Pages;

class DefensesDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'defensesDashboard';

    protected static ?string $title = 'Defenses dashboard';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Defenses dashboard';

    protected static ?string $navigationGroup = 'Dashboards';

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    public function getWidgets(): array
    {
        return [
            // \App\Filament\Administration\Widgets\InternshipsWordCloudWidget::class,
            \App\Filament\Administration\Widgets\CompletedDefensesRatioChart::class,
            \App\Filament\Administration\Widgets\DefensesPerProgramChart::class,
            \App\Filament\Administration\Widgets\AssignedSupervisorsReviewersChart::class,
            \App\Filament\Administration\Widgets\TimetableOverviewChart::class,
            \App\Filament\Administration\Widgets\ProfessorsParticipationTable::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }

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
}
