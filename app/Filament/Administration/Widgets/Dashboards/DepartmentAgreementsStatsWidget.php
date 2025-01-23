<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums\Department;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Professor;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class DepartmentAgreementsStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.department-agreements-stats';

    // Change to full width
    protected int | string | array $columnSpan = 'full';

    // Add listeners for department assignment events
    protected $listeners = [
        'departmentAssigned' => '$refresh',
        'departmentChanged' => '$refresh',
        'toggleStats' => 'toggleStatsView',
    ];

    public bool $showAlternativeStats = false;

    public function toggleStatsView()
    {
        $this->showAlternativeStats = ! $this->showAlternativeStats;
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }

    protected function getStats(): Collection
    {
        return collect(Department::cases())->map(function ($department) {
            if ($this->showAlternativeStats) {
                // Calculate based on project->professors->department relationship
                $projectCount = FinalYearInternshipAgreement::whereHas('project.professors', function ($query) use ($department) {
                    $query->where('department', $department->value);
                })->count();
            } else {
                // Original calculation based on assigned_department
                $projectCount = FinalYearInternshipAgreement::where('assigned_department', $department->value)->count();
            }

            $professorsCount = Professor::where('department', $department->value)->count();
            $ratio = $professorsCount ? $projectCount / $professorsCount : 0;

            return [
                'name' => $department->getLabel(),
                'description' => $department->getDescription(),
                'count' => $projectCount,
                'color' => $department->getColor(),
                'icon' => $department->getIcon(),
                'ratio' => $ratio,
                'professors_count' => $professorsCount,
            ];
        })->sortByDesc('count');
    }
}
