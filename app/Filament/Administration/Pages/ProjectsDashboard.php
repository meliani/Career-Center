<?php

namespace App\Filament\Administration\Pages;

class ProjectsDashboard extends \Filament\Pages\Dashboard
{
    // use \JibayMcs\FilamentTour\Tour\HasTour;

    protected static string $routePath = 'projects-dashboard';

    protected static ?string $title = 'projects-dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Projects Dashboard';

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
            return auth()->user()->isSuperAdministrator();

            return auth()->user()->can('viewAny', \App\Models\Project::class);
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
        $widgets = [];

        if (auth()->user()->isProfessor()) {
            // $widgets[] = \App\Filament\Administration\Widgets\ProfessorProjectsWidget::class;
        }
        if (auth()->user()->isAdministrator()) {
            $widgets[] = \App\Filament\Administration\Widgets\AdministratorProjectsWidget::class;
        }
        $widgets = array_merge($widgets, [
            // \App\Filament\Administration\Widgets\Dashboards\YearlyTimelineWidget::class,
        ]);

        return $widgets;
    }
}