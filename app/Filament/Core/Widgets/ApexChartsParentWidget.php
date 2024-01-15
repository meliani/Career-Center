<?php

namespace App\Filament\Core\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApexChartsParentWidget extends ApexChartWidget
{
    protected function getHeading(): ?string
    {
        return __(static::$heading);
    }
}