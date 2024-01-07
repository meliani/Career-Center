<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Internship;

class InternshipsPerOrganizationChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'internshipsPerOrganizationChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Internships per Organizations Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // get internships per organization_name
        // $data = Internship::query()
        //     ->select('organization_name')
        //     ->groupBy('organization_name')
        //     ->get();
            //  return apex chart data
            $internshipData = Internship::select('organization_name', \DB::raw('count(*) as count'))
            ->groupBy('organization_name')
            ->get()
            ->toArray();
// dd(array_column($internshipData, 'organization_name'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'organization_name'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
