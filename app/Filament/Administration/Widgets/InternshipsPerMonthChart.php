<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\FilamentChartsParentWidget as ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Internship;

class InternshipsPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Announced Internships per month';
    protected static ?string $type = 'bar';
    public function getDescription(): ?string
    {
        return __('Number of Internships per Month during the academic year');
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
        $data = $this->filter;
    }
}
