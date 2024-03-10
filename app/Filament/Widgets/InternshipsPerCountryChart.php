<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;

class InternshipsPerCountryChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 6;

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

        $internshipData = InternshipAgreement::select('country', \DB::raw('count(*) as count'))

            ->groupBy('country')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'donut',
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
