<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Internship;

class InternshipsPerCountryChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 2;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerCountryChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per Country';
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
        // get internships per Country_name
        // $data = Internship::query()
        //     ->select('Country_name')
        //     ->groupBy('Country_name')
        //     ->get();
        //  return apex chart data
        $internshipData = Internship::select('country', \DB::raw('count(*) as count'))
        // where internship.student.program like AMOA
            // ->whereHas('student', function ($q) {

            //     $q->where('program', auth()->user()->program_coordinator);
            // })
            ->groupBy('country')
            ->get()
            ->toArray();

        // dd(array_column($internshipData, 'country'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'country'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
