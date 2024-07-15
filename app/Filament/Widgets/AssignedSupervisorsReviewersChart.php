<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;

class AssignedSupervisorsReviewersChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    // protected int|string|array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'AssignedSupervisorsReviewersChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Assigned supervisors by department';

    public static function canView(): bool
    {
        return true;
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    public function getOptions(): array
    {
        $data = \DB::table('professor_project')
            ->join('projects', 'professor_project.project_id', '=', 'projects.id')
            ->join('users', 'professor_project.professor_id', '=', 'users.id')
            ->selectRaw("
            users.department as department_name,
            CASE
                WHEN jury_role IN ('Reviewer1', 'Reviewer2') THEN 'Reviewer'
                ELSE jury_role
            END as jury_role,
            COUNT(DISTINCT projects.id) AS total_projects
        ")
            ->whereIn('jury_role', ['Supervisor', 'Reviewer1', 'Reviewer2'])
            ->groupBy('users.department', \DB::raw("CASE WHEN jury_role IN ('Reviewer1', 'Reviewer2') THEN 'Reviewer' ELSE jury_role END"))
            ->get();

        // Assuming the need to display this data in a chart
        $chartData = $data->groupBy('department_name')->map(function ($items) {
            $roleCounts = [
                'Supervisor' => 0,
                'Reviewer' => 0, // Merged Reviewer1 and Reviewer2 into Reviewer
            ];
            foreach ($items as $item) {
                if (array_key_exists($item->jury_role, $roleCounts)) {
                    $roleCounts[$item->jury_role] += $item->total_projects;
                }
            }

            return $roleCounts;
        });

        // Preparing series data for the chart
        $series = [];
        foreach (['Supervisor', 'Reviewer'] as $role) {
            $series[] = [
                'name' => __($role),
                'data' => $chartData->pluck($role)->values()->all(),
            ];
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $chartData->keys()->all(),
            ],
        ];
    }
}
