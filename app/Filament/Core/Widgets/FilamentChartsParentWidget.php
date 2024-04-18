<?php

namespace App\Filament\Core\Widgets;

use Filament\Widgets\ChartWidget;

class FilamentChartsParentWidget extends ChartWidget
{
    protected static ?string $maxHeight = '400px';

    // protected function getHeading(): ?string
    // {
    //     return __(static::$heading);
    // }
    // protected function getFilters(): ?array
    // {
    //     return [
    //         'today' => 'Today',
    //         'week' => 'Last week',
    //         'month' => 'Last month',
    //         'year' => 'This year',
    //     ];
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
