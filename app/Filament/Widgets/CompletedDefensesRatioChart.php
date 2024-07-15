<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Project;
use App\Models\Year;

class CompletedDefensesRatioChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = '1';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'CompletedDefensesRatioChart';

    /**
     * Widget Title
     */
    // taux de réalisation des soutenance
    protected static ?string $heading = 'Completed defenses ratio';

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
        return __('An overview of the defenses distribution per status.');
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
    public function getOptions(): array
    {
        $defensesData = [
            // Example data
            ['defense_status' => 'Pending'],
            ['defense_status' => 'Authorized'],
            ['defense_status' => 'Completed'],
            ['defense_status' => 'Postponed'],
            // Add more data as needed
        ];

        $customLabels = [
            'Pending' => __('Pending authorization'),
            'Authorized' => __('Authorized'),
            'Completed' => __('Defense completed'),
            'Postponed' => __('Postponed'),
            // Add more mappings as needed
        ];
        $defensesData = \DB::table('projects')
            ->leftJoin('project_student', 'projects.id', '=', 'project_student.project_id')
            ->select(
                'defense_status',
                \DB::raw('COUNT(DISTINCT projects.id) as total_projects'),
                \DB::raw('COUNT(DISTINCT project_student.student_id) as total_students')
            )
            ->groupBy('defense_status')
            ->get();

        $categories = $defensesData->pluck('defense_status')->map(function ($status) use ($customLabels) {
            return $customLabels[$status] ?? $status;
        })->toArray();

        $totalProjects = $defensesData->pluck('total_projects')->toArray();
        $totalStudents = $defensesData->pluck('total_students')->toArray();
        // dd($totalProjects, $totalStudents);
        $customizedCategories = array_map(function ($status) use ($customLabels) {
            return $customLabels[$status] ?? $status;
        }, $categories);

        $seriesData = [
            [
                'name' => __('Total Students'),
                'data' => $totalStudents,
            ],
            [
                'name' => __('Total defenses'),
                'data' => $totalProjects,
            ],

        ];

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'toolbar' => [
                    'show' => true,
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
                'enabled' => true,
            ],
            'stroke' => [
                'show' => true,
                'width' => 2,
                'colors' => ['transparent'],
            ],
            'xaxis' => [
                'categories' => $categories,
            ],
            'series' => $seriesData,

            'yaxis' => [
                'title' => [
                    'text' => __('Number of defenses'),
                ],
            ],
            'fill' => [
                'opacity' => 1,
            ],
        ];

    }
}
