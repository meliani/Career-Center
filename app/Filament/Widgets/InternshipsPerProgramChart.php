<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;

class InternshipsPerProgramChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'InternshipsPerProgramChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Anounced Internships distribution per Program';

    public static function canView(): bool
    {
        return true;
    }

    public function getDescription(): ?string
    {
        return __('An overview of the announced internships distribution per program.');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $internshipData = \DB::table('students')
            ->leftJoin('internships', 'students.id', '=', 'internships.student_id')
            // ->leftJoin('projects', 'projects.id', '=', 'internships.project_id')
            ->where('students.year_id', 7)
            ->where('students.level', 'ThirdYear')
            ->groupBy('program')
            ->select(
                'program',
                \DB::raw('COUNT(DISTINCT students.id) AS total_students'),
                \DB::connection('backend_database')->raw('COUNT(internships.id) AS total_internships'),
                // \DB::connection('backend_database')->raw('COUNT(projects.id) AS total_projects'),
                \DB::connection('backend_database')->raw('ROUND((COUNT(internships.id) / COUNT(DISTINCT students.id)) * 100, 2) AS percentage')
            )
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'with: 100%',
            ],
            'series' => [
                [
                    'name' => __('Total Students'),
                    'data' => array_column($internshipData, 'total_students'),
                ],
                [
                    'name' => __('Total Announced Internships'),
                    'data' => array_column($internshipData, 'total_internships'),
                ],
                [
                    'name' => __('Ratio (%)'),
                    'data' => array_column($internshipData, 'percentage'),
                ],
            ],
            'labels' => array_column($internshipData, 'program'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                    'color' => '#ffffff',
                    'fontColor' => '#ffffff',
                ],
            ],
            'xaxis' => [
                'categories' => array_column($internshipData, 'program'),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                    'dataLabels' => [
                        'position' => 'top',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'position' => 'top',
                'style' => [
                    'colors' => ['#fff'],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'show' => false,
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontSize' => '12px',
                        'fontFamily' => 'Helvetica, Arial, sans-serif',
                        'cssClass' => 'apexcharts-xaxis-label',
                    ],
                ],
            ],
        ];
    }
}
