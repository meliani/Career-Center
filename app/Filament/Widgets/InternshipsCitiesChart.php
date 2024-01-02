<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Internship;

class InternshipsCitiesChart extends ChartWidget
{
    protected static ?string $heading = 'Internships Chart';
    public function getDescription(): ?string
    {
        return 'The number of Internships per month.';
    }
    protected function getData(): array
    {
        $data = Trend::model(Internship::class)
        ->between(
            // start: now()->startOfYear(),
            start: now()->subMonths(4)->startOfMonth(),
            end: now()->endOfMonth(),
            // end: now()->endOfYear(), ===
        )
        ->perMonth()
        ->count();
 
    return [
        'datasets' => [
            [
                'label' => 'Internships per month',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
