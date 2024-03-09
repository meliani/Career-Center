<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;

class InternshipsPerCityChartMorocco extends ApexChartsParentWidget
{
    protected static ?int $sort = 3;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsPerCityChartMorocco';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Announced Internships per City (Morocco)';

    protected static ?int $contentHeight = 300; //px

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
            ->where('country', 'Morocco')
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
