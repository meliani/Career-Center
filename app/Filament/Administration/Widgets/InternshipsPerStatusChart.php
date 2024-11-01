<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;

class InternshipsPerStatusChart extends ApexChartsParentWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerStatusChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Internships advancement status';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    public static function canView(): bool
    {
        return false;
    }

    protected function getOptions(): array
    {
        // get internships per organization_name
        // $data = InternshipAgreement::query()
        //     ->select('organization_name')
        //     ->groupBy('organization_name')
        //     ->get();
        //  return apex chart data
        $internshipData = InternshipAgreement::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->toArray();

        // dd(array_column($internshipData, 'organization_name'));
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'status'),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }

    public static function shouldAutoDiscover(): bool
    {
        return false;
    }
}
