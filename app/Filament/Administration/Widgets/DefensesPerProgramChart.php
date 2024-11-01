<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Year;

class DefensesPerProgramChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'DefensesPerProgramChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Defenses progress per program';

    public static function canView(): bool
    {
        return true;
    }

    public function getHeading(): string
    {
        return __(self::$heading) . ' - ' . Year::current()->title;
    }

    public function getDescription(): ?string
    {
        return __('An overview of the defenses distribution per program.');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     *
     *
     **/
    public function getOptions(): array
    {
        $defensesData = \DB::table('students')
            ->join('project_student', 'students.id', '=', 'project_student.student_id')
            ->join('projects', 'project_student.project_id', '=', 'projects.id')
            ->selectRaw('
            students.program,
            COUNT(DISTINCT projects.id) AS total_projects,
            COUNT(DISTINCT CASE WHEN projects.defense_status = \'completed\' THEN projects.id END) AS total_defenses,
            ROUND((COUNT(DISTINCT CASE WHEN projects.defense_status = \'completed\' THEN projects.id END) / COUNT(DISTINCT projects.id)) * 100, 1) AS percentage
        ')
            ->whereNotNull('projects.id') // Ensure only students with projects are counted
            ->groupBy('students.program')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%', // Corrected typo from 'with: 100%' to 'width' => '100%'
            ],
            'series' => [
                [
                    'name' => __('Total final projects'),
                    'data' => array_column($defensesData->toArray(), 'total_projects'),
                ],
                [
                    'name' => __('Total completed defenses'),
                    'data' => array_column($defensesData->toArray(), 'total_defenses'),
                ],
                [
                    'name' => __('Percentage of achievement (%)'),
                    'data' => array_column($defensesData->toArray(), 'percentage'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($defensesData->toArray(), 'program'),
            ],
            // Optionally, uncomment and adjust the yaxis labels if needed
            // 'yaxis' => [
            //     'labels' => [
            //         'formatter' => function($value) { return $value . '%'; },
            //     ],
            // ],
        ];

    }
}
