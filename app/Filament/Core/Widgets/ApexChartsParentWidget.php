<?php

namespace App\Filament\Core\Widgets;

use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApexChartsParentWidget extends ApexChartWidget
{
    protected function getHeading(): ?string
    {
        return __(static::$heading);
    }

    protected function getLoadingIndicator(): null | string | View
    {
        return view('components.loading-indicator');
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
