<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;

class InternshipsPerCityChartFrance extends ApexChartsParentWidget
{
    protected static ?int $sort = 8;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerCityChartFrance';

    protected static ?int $contentHeight = 300; //px

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per City (France)';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $internshipData = InternshipAgreement::select(
            'city',
            \DB::raw('count(*) as count')
        )
            ->where('country', 'France')
            ->groupBy('city')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'donut',
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
