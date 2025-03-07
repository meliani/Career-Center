<?php

namespace App\Filament\Administration\Widgets;

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
            'users.department',
            \DB::raw('count(*) as count')
        )->join('professor_projects', 'users.id', '=', 'professor_projects.professor_id')
            // ->where('users.department', '!=', '')
            ->groupBy('department')
            ->get()
            ->toArray();

        foreach ($projects as $key => $value) {
            if (empty($value['department'])) {
                $projects[$key]['department'] = __('Unknown');
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
            'labels' => array_column($projects, 'department'),
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
