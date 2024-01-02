<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Internship;

class InternshipsCountriesChart extends ChartWidget
{
    protected static ?string $heading = 'Internships Chart';
    
    public function getDescription(): ?string
    {
        return 'The number of Internships per Country.';
    }

    protected function getData(): array
    {
        $data = Trend::query(Internship::query())/* ->groupBy('country') */
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->aggregate('country', 'count');

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
