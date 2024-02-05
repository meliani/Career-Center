<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Student;

class InternshipsPerProgramChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

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
        // get internships per organization_name
        // $data = Internship::query()
        //     ->select('organization_name')
        //     ->groupBy('organization_name')
        //     ->get();
        //  return apex chart data

        // get number of internships per program




        // dd($internshipData);

        // calculate ratio of each program number of students with numbers of students that had Announced internship


        /*         $internshipData = Student::query()->select('program', \DB::raw('count(*) as count'))
            ->whereHas(
                'internship',
                function ($q) {
                    $q->select(
                        'id',
                        \DB::raw('count(*) as count'),
                        \DB::raw('sum(case when id is not null then 1 else 0 end) / count(*) as internship_ratio')
                    );
                }
            )
            ->groupBy('program')
            ->get('program', 'count')
            ->makeHidden(['full_name'])
            ->toArray(); */
        // $internshipData = Student::query()
        //     ->select('program',
        //         \DB::raw('count(*) as total_students'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) as students_with_internship'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) / count(*) as internship_ratio')
        //     )
        //     ->groupBy('program')
        //     ->get();
        // $internshipData = Student::query()
        // ->select('program', \DB::raw('COUNT(*) as total_students'), \DB::raw('SUM(CASE WHEN id IS NOT NULL THEN 1 ELSE 0 END) as internship_students'))
        // ->groupBy('program')
        // ->get()
        // ->map(function ($program) {
        //     return [
        //         'program' => $program->program,
        //         'total_students' => $program->total_students,
        //         'internship_students' => $program->internship_students,
        //         'internship_ratio' => $program->total_students > 0 ? $program->internship_students / $program->total_students : 0,
        //     ];
        // });

        /*         $percentages = \DB::connection('frontend_database')
            ->table('people')
            ->leftjoin('internships', 'people.id', '=', 'internships.student_id')
            ->groupBy('program')
            ->select(
                'program',
                \DB::connection('frontend_database')->raw('count(distinct people.id) as total_students'),
                \DB::connection('frontend_database')->raw('count(internships.id) as total_internships'),
                \DB::connection('frontend_database')->raw('round((count(internships.id) / count(distinct people.id)) * 100, 2) as percentage')
            )
            ->get(); */

        // $internshipData = Student::on('frontend_database')
        //     ->leftJoin('internships', 'people.id', '=', 'internships.student_id')
        //     // ->where('people.year_id', 7)
        //     // ->where('current_year', 3)
        //     // ->whereHas(
        //     //     'internship',
        //     //     function ($q) {
        //     //         $q->where(
        //     //         );
        //     //     }
        //     // )
        //     ->groupBy('program')
        //     ->select(
        //         'program',
        //         \DB::raw('COUNT(DISTINCT people.id) AS total_students'),
        //         \DB::raw('COUNT(internships.id) AS total_internships'),
        //         \DB::raw('ROUND((COUNT(internships.id) / COUNT(DISTINCT people.id)) * 100, 2) AS percentage')
        //     )
        //     ->get()
        //     ->map(function ($program) {
        //         return [
        //             'program' => $program->program,
        //             'total_students' => $program->total_students,
        //             'total_internships' => $program->total_internships,
        //             'percentage' => $program->percentage,
        //         ];
        //     });

        $internshipData = \DB::connection('frontend_database')->table('people')
            ->leftJoin('internships', 'people.id', '=', 'internships.student_id')
            ->where('people.year_id', 7)
            ->groupBy('program')
            ->select(
                'program',
                \DB::connection('frontend_database')->raw('COUNT(DISTINCT people.id) AS total_students'),
                \DB::connection('frontend_database')->raw('COUNT(internships.id) AS total_internships'),
                \DB::connection('frontend_database')->raw('ROUND((COUNT(internships.id) / COUNT(DISTINCT people.id)) * 100, 2) AS percentage')
            )
            ->get()
            ->toArray();
            // ->map(function ($program) {
            //     return [
            //         'program' => $program->program,
            //         'total_students' => $program->total_students,
            //         'total_internships' => $program->total_internships,
            //         'percentage' => $program->percentage,
            //     ];
            // });
        // dd($internshipData);

        // dd($percentages);
        // dd(array_column($internshipData, 'organization_name'));
        // return bar chart with percetage , total students and total internships
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Students',
                    'data' => array_column($internshipData, 'total_students'),
                ],
                [
                    'name' => 'Total Internships',
                    'data' => array_column($internshipData, 'total_internships'),
                ],
                [
                    'name' => 'Percentage',
                    'data' => array_column($internshipData, 'percentage'),
                ],
            ],
            'labels' => array_column($internshipData, 'program'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];

/*         return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'total_students'),
            'labels' => array_column($internshipData, 'program'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ]; */
    }
}
