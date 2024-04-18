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
    protected static ?string $heading = 'Anounced Internships per Program';

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
        $internshipData = \DB::table('students')
            ->leftJoin('internships', 'students.id', '=', 'internships.student_id')
            ->where('students.year_id', 7)
            ->groupBy('program')
            ->select(
                'program',
                \DB::raw('COUNT(DISTINCT students.id) AS total_students'),
                \DB::connection('backend_database')->raw('COUNT(internships.id) AS total_internships'),
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
                    'name' => __('Percentage'),
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
        ];
    }
}
