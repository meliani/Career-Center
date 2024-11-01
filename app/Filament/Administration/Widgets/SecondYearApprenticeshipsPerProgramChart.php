<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;

class SecondYearApprenticeshipsPerProgramChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = '1';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'SecondYearApprenticeshipsPerProgramChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Second year internships per program';

    public static function canView(): bool
    {
        return true;
    }

    public function getDescription(): ?string
    {
        return __('This chart shows the number of internships done by second-year students per program.');
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
        $apprenticeships = \DB::table('apprenticeships')
            ->leftJoin('students', 'apprenticeships.student_id', '=', 'students.id')
            ->where('students.level', 'SecondYear')
            ->groupBy('program')
            ->select(
                'program',
                \DB::raw('count(*) as total')
            )
            ->get();

        $programs = $apprenticeships->pluck('program')->toArray();

        $totalApprenticeships = $apprenticeships->pluck('total')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'endingShape' => 'rounded',
                    'columnWidth' => '55%',
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'show' => true,
                'width' => 2,
                'colors' => ['transparent'],
            ],
            'series' => [
                [
                    'name' => __('Apprenticeships'),
                    'data' => $totalApprenticeships,
                ],
            ],
            'xaxis' => [
                'categories' => $programs,
            ],
            'yaxis' => [
                'title' => [
                    'text' => __('Number of Apprenticeships'),
                ],
            ],

        ];

    }
}
