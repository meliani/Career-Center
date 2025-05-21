<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Year;

class AssignedSupervisorsReviewersChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    // protected int|string|array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'AssignedSupervisorsReviewersChart';

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
    public function getOptions(): array
    {
        $currentYear = Year::current();
        
        // Set dynamic heading with current year
        static::$heading = 'Assigned supervisors by department (' . $currentYear->title . ')';

        $data = \DB::table('professor_projects')
            ->join('projects', 'professor_projects.project_id', '=', 'projects.id')
            ->join('users', 'professor_projects.professor_id', '=', 'users.id')
            ->join('project_agreements', 'projects.id', '=', 'project_agreements.project_id')
            ->join('final_year_internship_agreements', function ($join) use ($currentYear) {
                $join->on('project_agreements.agreeable_id', '=', 'final_year_internship_agreements.id')
                    ->where('project_agreements.agreeable_type', '=', 'App\\Models\\FinalYearInternshipAgreement')
                    ->where('final_year_internship_agreements.year_id', '=', $currentYear->id);
            })
            ->selectRaw("
            users.department as department_name,
            CASE
                WHEN jury_role IN ('Reviewer1', 'Reviewer2') THEN 'Reviewer'
                ELSE jury_role
            END as jury_role,
            COUNT(DISTINCT projects.id) AS total_projects
        ")
            ->whereIn('jury_role', ['Supervisor', 'Reviewer1', 'Reviewer2'])
            ->groupBy('users.department', \DB::raw("CASE WHEN jury_role IN ('Reviewer1', 'Reviewer2') THEN 'Reviewer' ELSE jury_role END"))
            ->get();

        $chartData = $data->groupBy('department_name')->map(function ($items) {
            $roleCounts = [
                'Supervisor' => 0,
                'Reviewer' => 0, // Merged Reviewer1 and Reviewer2 into Reviewer
            ];
            foreach ($items as $item) {
                if (array_key_exists($item->jury_role, $roleCounts)) {
                    $roleCounts[$item->jury_role] += $item->total_projects;
                }
            }

            return $roleCounts;
        });

        // Preparing series data for the chart
        $series = [];
        foreach (['Supervisor', 'Reviewer'] as $role) {
            $series[] = [
                'name' => __($role),
                'data' => $chartData->pluck($role)->values()->all(),
            ];
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $chartData->keys()->all(),
            ],
        ];
    }
}
