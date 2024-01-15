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

        $internshipData = Student::query()->select('filiere_text', \DB::raw('count(*) as count'))
            ->whereHas('internship',
                function ($q) {
                    $q->select('filiere_text', \DB::raw('count(*) as count'),
                        \DB::raw('sum(case when id is not null then 1 else 0 end) / count(*) as internship_ratio')
                    );
                }
            )
            ->groupBy('filiere_text')
            ->get('filiere_text', 'count')
            ->makeHidden(['full_name'])
            ->toArray();
        // $internshipData = Student::query()
        //     ->select('filiere_text',
        //         \DB::raw('count(*) as total_students'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) as students_with_internship'),
        //         \DB::raw('sum(case when internship_id is not null then 1 else 0 end) / count(*) as internship_ratio')
        //     )
        //     ->groupBy('filiere_text')
        //     ->get();

        // dd($internshipData);

        // dd(array_column($internshipData, 'organization_name'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'filiere_text'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
