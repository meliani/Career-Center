<?php

namespace App\Filament\Administration\Pages;

class GeneralStatsDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'general-stats-dashboard';

    protected static ?string $title = 'General Statistics Dashboard';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'General Statistics Dashboard';

    protected static ?string $navigationGroup = 'Dashboards';

    // columns

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

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
            // return auth()->user()->isAdministrator();

            return auth()->user()->can('viewAny', \App\Models\Project::class);
        } else {
            return false;
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Administration\Widgets\Dashboards\DepartmentAgreementsStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        $widgets = [];

        if (auth()->user()->isProfessor()) {
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\AdminGettingStartedWidget::class;
            $widgets[] = \App\Filament\Administration\Widgets\FinalInternshipsPerProgramChart::class;

            // $widgets[] = \App\Filament\Administration\Widgets\ProfessorProjectsWidget::class;
        }

        if (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor() || auth()->user()->isDirection()) {
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\AdminGettingStartedWidget::class;
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\YearlyTimelineWidget::class;
            $widgets[] = \App\Filament\Administration\Widgets\FinalInternshipsPerProgramChart::class;

        }

        $widgets = array_merge($widgets, [
        ]);

        return $widgets;
    }
}
