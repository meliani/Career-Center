<?php

namespace App\Filament\Widgets;

use App\Enums\DefenseStatus;
use App\Filament\Core\Widgets\ApexChartsParentWidget;

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
    protected static ?string $heading = 'Defenses distribution per Program';

    public static function canView(): bool
    {
        return true;
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

    /**
     * Data comes from projects
     * Each project is linked to multiple timetables through the timetables table, which contains project_id and timeslot_id.
     * Each timetable is linked to a specific timeslot.
     * Each project has a defense_status that can be 'Approved', 'Completed', 'Postponed', or null.
     * The 'program' is an Enum in the student model, indicating the program the student is enrolled in.
     * To find the student_id associated with a project, we must look into the internships table, which contains both student_id and project_id.
     */
    protected function getOptions(): array
    {
        $defensesData = \DB::table('projects')
            ->leftJoin('internships', 'projects.id', '=', 'internships.project_id')
            ->leftJoin('students', 'internships.student_id', '=', 'students.id')
            ->leftJoin('timetables', 'projects.id', '=', 'timetables.project_id')
            // ->leftJoin('timeslots', 'timetables.timeslot_id', '=', 'timeslots.id')
            // ->where('projects.defense_status', DefenseStatus::Completed)
            ->groupBy('students.program')
            ->select(
                'students.program',
                \DB::raw('COUNT(DISTINCT projects.id) AS total_projects'),
                \DB::raw('COUNT(DISTINCT projects.defense_status = "Completed") AS total_defenses'),
                \DB::raw('ROUND((COUNT(DISTINCT projects.defense_status = "Completed") / COUNT(DISTINCT projects.id)) * 100, 2) AS percentage')
            )
            ->get()
            ->toArray();

        // dd($defensesData);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'with: 100%',
            ],
            'series' => [
                [
                    'name' => __('Total Projects'),
                    'data' => array_column($defensesData, 'total_projects'),
                ],
                [
                    'name' => __('Total Defenses'),
                    'data' => array_column($defensesData, 'total_defenses'),
                ],
                [
                    'name' => __('Ratio (%)'),
                    'data' => array_column($defensesData, 'percentage'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($defensesData, 'program'),
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'endingShape' => 'rounded',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'stroke' => [
                'show' => true,
                'width' => 2,
                'colors' => ['transparent'],
            ],
            'grid' => [
                'show' => false,
                'padding' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
            ],
            'yaxis' => [
                'show' => false,
                'title' => [
                    'text' => __('Number of projects'),
                ],
            ],
            'xaxis' => [
                'title' => [
                    'text' => __('Program'),
                ],
            ],
            'tooltip' => [
                'enabled' => true,
                'y' => [
                    'formatter' => 'function (val) { return val }',
                ],
            ],
        ];

    }
}
