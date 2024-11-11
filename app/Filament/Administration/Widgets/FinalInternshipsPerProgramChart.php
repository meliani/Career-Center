<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Year;

class FinalInternshipsPerProgramChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $chartId = 'FinalInternshipsPerProgramChart';

    protected static ?string $heading = 'Final Year Internships distribution per Program';

    public static function canView(): bool
    {
        return true;
    }

    public function getDescription(): ?string
    {
        return __('An overview of the final year internships distribution per program.');
    }

    protected function getOptions(): array
    {
        $internshipData = \DB::table('students')
            ->leftJoin('final_year_internship_agreements', 'students.id', '=', 'final_year_internship_agreements.student_id')
            ->where('students.year_id', Year::current()->id)
            ->where('students.level', 'ThirdYear')
            ->groupBy('students.program')
            ->select(
                'students.program',
                \DB::raw('COUNT(DISTINCT students.id) AS total_students'),
                \DB::raw('COUNT(final_year_internship_agreements.id) AS total_internships'),
                \DB::raw('ROUND(COALESCE(COUNT(final_year_internship_agreements.id) * 100.0 / NULLIF(COUNT(DISTINCT students.id), 0), 0), 1) AS percentage')
            )
            ->get();

        if ($internshipData->isEmpty()) {
            return $this->getEmptyStateOptions();
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
                'toolbar' => [
                    'show' => true,
                ],
                'dropShadow' => [
                    'enabled' => true,
                    'blur' => 3,
                    'opacity' => 0.2,
                ],
                'background' => 'transparent',
            ],
            'series' => [
                [
                    'name' => __('Total Students'),
                    'type' => 'column',
                    'data' => $internshipData->pluck('total_students')->toArray(),
                ],
                [
                    'name' => __('Total Final Year Internships'),
                    'type' => 'column',
                    'data' => $internshipData->pluck('total_internships')->toArray(),
                ],
                [
                    'name' => __('Success Rate (%)'),
                    'type' => 'line',
                    'data' => $internshipData->pluck('percentage')->toArray(),
                ],
            ],
            'colors' => ['#3B82F6', '#10B981', '#F59E0B'],  // Bright blue, green, and amber
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                    'endingShape' => 'rounded',
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'enabledOnSeries' => [0, 1],
                'style' => [
                    'fontSize' => '12px',
                    'fontWeight' => 600,
                    'colors' => ['#1F2937'],
                ],
                'background' => [
                    'enabled' => true,
                    'foreColor' => '#fff',
                    'padding' => 4,
                    'borderRadius' => 4,
                    'borderWidth' => 0,
                    'opacity' => 0.9,
                ],
            ],
            'stroke' => [
                'width' => [0, 0, 4],
                'curve' => 'smooth',
                'lineCap' => 'round',
            ],
            'grid' => [
                'borderColor' => '#E5E7EB',
                'strokeDashArray' => 4,
                'xaxis' => [
                    'lines' => [
                        'show' => true,
                    ],
                ],
                'padding' => [
                    'top' => -10,
                ],
            ],
            'xaxis' => [
                'categories' => $internshipData->pluck('program')->toArray(),
                'title' => [
                    'text' => __('Programs'),
                    'style' => [
                        'fontSize' => '12px',
                        'fontWeight' => 600,
                        'color' => '#4B5563',
                    ],
                ],
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                        'fontWeight' => 600,
                        'colors' => '#4B5563',
                    ],
                ],
                'axisBorder' => [
                    'show' => false,
                ],
                'axisTicks' => [
                    'show' => false,
                ],
            ],
            'yaxis' => [
                [
                    'title' => [
                        'text' => __('Number of Students'),
                        'style' => [
                            'fontSize' => '12px',
                            'fontWeight' => 600,
                            'color' => '#4B5563',
                        ],
                    ],
                    'labels' => [
                        'style' => [
                            'colors' => '#4B5563',
                            'fontSize' => '12px',
                            'fontWeight' => 500,
                        ],
                    ],
                ],
                [
                    'opposite' => true,
                    'title' => [
                        'text' => __('Success Rate (%)'),
                        'style' => [
                            'fontSize' => '12px',
                            'fontWeight' => 600,
                            'color' => '#4B5563',
                        ],
                    ],
                    'labels' => [
                        'style' => [
                            'colors' => '#4B5563',
                            'fontSize' => '12px',
                            'fontWeight' => 500,
                        ],
                    ],
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'legend' => [
                'position' => 'bottom',
                'offsetY' => 7,
                'fontSize' => '13px',
                'fontWeight' => 600,
                'markers' => [
                    'radius' => 12,
                ],
            ],
            'fill' => [
                'opacity' => [0.85, 0.85, 1],
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.9,
                    'opacityTo' => 0.7,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
                'theme' => 'light',
                'style' => [
                    'fontSize' => '12px',
                ],
                'y' => [
                    [
                        'formatter' => 'function (y) { return y + " Students" }',
                    ],
                    [
                        'formatter' => 'function (y) { return y + " Internships" }',
                    ],
                    [
                        'formatter' => 'function (y) { return y + "%" }',
                    ],
                ],
            ],
        ];
    }

    private function getEmptyStateOptions(): array
    {
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
            ],
            'series' => [
                ['name' => __('Total Students'), 'data' => [0]],
                ['name' => __('Total Final Year Internships'), 'data' => [0]],
                ['name' => __('Success Rate (%)'), 'data' => [0]],
            ],
            'xaxis' => [
                'categories' => [__('No Data Available')],
            ],
            'noData' => [
                'text' => __('No data available for the selected period'),
                'align' => 'center',
                'verticalAlign' => 'middle',
                'style' => [
                    'fontSize' => '16px',
                ],
            ],
        ];
    }
}
