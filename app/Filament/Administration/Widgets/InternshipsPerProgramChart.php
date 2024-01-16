<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Student;

class InternshipsPerProgramChart extends ApexChartsParentWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'InternshipsPerProgramChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Anounced Internships per Program';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // get internships per organization_name
        // $data = Internship::query()
        //     ->select('organization_name')
        //     ->groupBy('organization_name')
        //     ->get();
        //  return apex chart data

        // get number of internships per program

        


// dd($internshipData);

// calculate ratio of each program number of students with numbers of students that had Announced internship


        $internshipData = Student::query()->select('program', \DB::raw('count(*) as count'))
            ->whereHas('internship',
                function ($q) {
                    $q->select('id', \DB::raw('count(*) as count'),
                        \DB::raw('sum(case when id is not null then 1 else 0 end) / count(*) as internship_ratio')
                    );
                }
            )
            ->groupBy('program')
            ->get('program', 'count')
            ->makeHidden(['full_name'])
            ->toArray();
        // $internshipData = Student::query()
        //     ->select('program',
        //         \DB::raw('count(*) as total_students'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) as students_with_internship'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) / count(*) as internship_ratio')
        //     )
        //     ->groupBy('program')
        //     ->get();
        $internshipData = Student::query()
        ->select('program', \DB::raw('COUNT(*) as total_students'), \DB::raw('SUM(CASE WHEN id IS NOT NULL THEN 1 ELSE 0 END) as internship_students'))
        ->groupBy('program')
        ->get()
        ->map(function ($program) {
            return [
                'program' => $program->program,
                'total_students' => $program->total_students,
                'internship_students' => $program->internship_students,
                'internship_ratio' => $program->total_students > 0 ? $program->internship_students / $program->total_students : 0,
            ];
        });
        dd($internshipData);

        // dd(array_column($internshipData, 'organization_name'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'program'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
