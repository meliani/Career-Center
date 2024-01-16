<?php

namespace App\Filament\Core\Widgets;

use Filament\Widgets\ChartWidget;

class FilamentChartsParentWidget extends ChartWidget
{
    // protected function getHeading(): ?string
    // {
    //     return __(static::$heading);
    // }

    public function getDescription(): ?string
    {
        return __(static::$description);
    }
    protected function getType(): string
    {
        return static::$type;
    }
    
    public function getHeading(): string
    {
        return __(static::$heading);
    }
}
