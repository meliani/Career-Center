<?php

namespace App\Filament\ProgramCoordinator\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Internship;

class InternshipsPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Announced  Internships per month.';
    
    public function getDescription(): ?string
    {
        return __('Number of Internships per Month during the academic year.');
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
