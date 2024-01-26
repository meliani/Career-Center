<?php

namespace App\Filament\Core\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Contracts\View\View;

class ApexChartsParentWidget extends ApexChartWidget
{
    protected function getHeading(): ?string
    {
        return __(static::$heading);
    }
    // protected function getFooter(): string|View
    // {
    //     return __(static::$footer);
    // }
    // protected function getContentHeight(): ?int
    // {
    //     return 300;
    // }
    public static function canView(): bool
    {

        return false;
    }
}
