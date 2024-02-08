<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Internship;

class InternshipsPerOrganizationChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 10;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerOrganizationChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per Organization';

    protected function getContentHeight(): ?int
    {
        return 600;
    }

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
        $internshipData = Internship::select('organization_name', \DB::raw('count(*) as count'))
            ->groupBy('organization_name')
            ->get()
            ->toArray();

        // dd(array_column($internshipData, 'organization_name'));
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 600,
            ],
            'series' => array_column($internshipData, 'count'),
            'labels' => array_column($internshipData, 'organization_name'),
            // display legend on the bottom
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }

    protected function getHeading(): ?string
    {
        return __(static::$heading);
    }
}
