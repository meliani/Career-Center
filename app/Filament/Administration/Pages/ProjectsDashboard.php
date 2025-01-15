<?php

namespace App\Filament\Administration\Pages;

class ProjectsDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'projects-dashboard';

    protected static ?string $title = 'Projects Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Projects Dashboard';

    protected static ?string $navigationGroup = 'Dashboards';

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
            // return auth()->user()->isSuperAdministrator();
            return auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrator();

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

    // Add this method to force full width for header widgets
    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        $widgets = [];
        if (auth()->user()->isProgramCoordinator()) {
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\ProgramCoordinatorAgreementsWidget::class;
        }

        if (auth()->user()->isDepartmentHead()) {
            // Replace widget with Livewire component
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\DepartmentProjectAssignments::class;
        }

        if (auth()->user()->isProfessor()) {
            // $widgets[] = \App\Filament\Administration\Widgets\ProfessorProjectsWidget::class;
        }

        if (auth()->user()->isAdministrator()) {
            // $widgets[] = \App\Filament\Administration\Widgets\AdministratorProjectsWidget::class;
            $widgets[] = \App\Filament\Administration\Widgets\Dashboards\AdvisingManagerWidget::class;
        }

        $widgets = array_merge($widgets, [
            // \App\Filament\Administration\Widgets\Dashboards\YearlyTimelineWidget::class,
        ]);

        return $widgets;
    }
}
