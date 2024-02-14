<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Project;
use App\Models\User;
use App\Enums;


class DepartmentProjectsPerformanceChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    // protected int|string|array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'DepartmentProjectsPerformanceChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Departments participation in projects';

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
            'users.department',
            \DB::raw('count(*) as count')
        )->join('professor_project', 'users.id', '=', 'professor_project.professor_id')
            // ->where('users.department', '!=', '')
            ->groupBy('department')
            ->get()
            ->toArray();

        foreach ($projects as $key => $value) {
            if (empty($value['department'])) {
                $projects[$key]['department'] = 'Unknown';
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
                    'name' => 'Total Participations',
                    'data' => array_column($projects, 'count'),
                ]
            ],
            'labels' => array_column($projects, 'department'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                    'color' => '#ffffff',
                    'fontColor' => '#ffffff',
                ],
            ],
        ];
    }
}
