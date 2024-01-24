<?php

namespace App\Filament\ProgramCoordinator\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Internship;

class InternshipsPerCityChart extends ApexChartsParentWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerCityChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per City';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // get internships per City_name
        // $data = Internship::query()
        //     ->select('City_name')
        //     ->groupBy('City_name')
        //     ->get();
        //  return apex chart data
        $internshipData = Internship::select('city', \DB::raw('count(*) as count'))
        // where internship.student.program like AMOA
            ->whereHas('student', function ($q) {

                $q->where('program', auth()->user()->program_coordinator);
            })
            ->groupBy('city')
            ->get()
            ->toArray();

        // dd(array_column($internshipData, 'city'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'city'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
