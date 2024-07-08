<?php

namespace App\Filament\Widgets;

use App\Enums\DefenseStatus;
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
            // ->leftJoin('timetables', 'projects.id', '=', 'timetables.project_id')
            // ->leftJoin('timeslots', 'timetables.timeslot_id', '=', 'timeslots.id')
            // ->where('projects.defense_status', DefenseStatus::Completed)
            ->groupBy('program')
            ->select(
                'program',
                \DB::raw('count(projects.id) as total_projects'),
                \DB::raw('count(projects.defense_status = "Completed") as total_defenses'),
                \DB::raw('ROUND(count(projects.defense_status = "Completed") / count(projects.id) * 100,1) as percentage')
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
                    'name' => __('Total final projects'),
                    'data' => array_column($defensesData, 'total_projects'),
                ],
                [
                    'name' => __('Total completed defenses'),
                    'data' => array_column($defensesData, 'total_defenses'),
                ],
                [
                    'name' => __('Percentage of achievement (%)'),
                    'data' => array_column($defensesData, 'percentage'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($defensesData, 'program'),
            ],
            'yaxis' => [
                // 'labels' => [
                //     'formatter' => fn ($defensesData) => array_column($defensesData, 'program'),
                // ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];

    }
}
