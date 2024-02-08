<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Internship;

class InternshipsPerCityChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 3;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerCityChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per City';

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
        $internshipData = Internship::select('city', \DB::raw('count(*) as count'))
            ->groupBy('city')
            ->get()
            ->toArray();

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
