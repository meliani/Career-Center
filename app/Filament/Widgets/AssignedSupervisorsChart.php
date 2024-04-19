<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\User;

class AssignedSupervisorsChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    // protected int|string|array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'AssignedSupervisorsChart';

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
    protected function getOptions(): array
    {
        // count of projects per department
        $projects = User::select(
            'internships.assigned_department',
            \DB::raw('count(*) as count')
        )->join('professor_project', 'users.id', '=', 'professor_project.professor_id')
            ->join('projects', 'professor_project.project_id', '=', 'projects.id')
            ->join('internships', 'projects.id', '=', 'internships.project_id')
            // ->where('users.department', '!=', '')
            ->groupBy('internships.assigned_department')
            ->get()
            ->toArray();

        foreach ($projects as $key => $value) {
            if (empty($value['assigned_department'])) {
                $projects[$key]['assigned_department'] = __('Not assigned');
            }
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'with: 100%',
            ],
            'series' => [
                [
                    'name' => __('Assigned supervisors'),
                    'data' => array_column($projects, 'count'),
                ],
            ],
            'labels' => array_column($projects, 'assigned_department'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                    'color' => '#ffffff',
                    'fontColor' => '#ffffff',
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'endingShape' => 'rounded',
                    'borderRadius' => 3,
                ],
            ],
        ];
    }
}
