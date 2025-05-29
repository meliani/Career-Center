<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums\Department;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Professor;
use App\Models\Year;
use App\Services\ProjectStatisticsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

#[Lazy]
class DepartmentAgreementsStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.department-agreements-stats';

    // Change to full width
    protected int | string | array $columnSpan = 'full';

    // Add listeners for department assignment events
    protected $listeners = [
        'departmentAssigned' => '$refresh',
        'departmentChanged' => '$refresh',
    ];

    public function mount()
    {
        // Widget now shows mentoring statistics by default
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }

    protected function getStats(): Collection
    {
        $year = Year::current();
        $statisticsService = new ProjectStatisticsService($year);
        
        // Get mentoring statistics instead of simple project counts
        return $statisticsService->getMentoringStatisticsSorted()->map(function ($stats) use ($statisticsService) {
            $department = $stats['department'];
            $projectsCount = $statisticsService->getProjectsByProfessorDepartment($department);
            
            return [
                'name' => $stats['department_name'],
                'description' => $department->getDescription(),
                'count' => $stats['total_avg'], // Use total average as the main metric
                'color' => $department->getColor(),
                'icon' => $department->getIcon(),
                'ratio' => $stats['total_avg'], // Total mentoring average
                'professors_count' => $stats['professors_count'],
                'projects_count' => $projectsCount,
                'avg_supervising' => $stats['avg_supervising'],
                'avg_reviewing' => $stats['avg_reviewing'],
                'total_avg' => $stats['total_avg'],
                'department' => $department,
            ];
        });
    }
}
