<?php

namespace App\Filament\ProgramCoordinator\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Internship;

class InternshipsPerWeekChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Internships per week';
    public function getDescription(): ?string
    {
        return __('Daily number of announced Internships.');
    }
    protected function getData(): array
    {
        $data = Trend::model(Internship::class)
        ->between(
            // start: now()->startOfYear(),
            start: now()->subWeeks(8)->startOfWeek(),
            end: now()->endOfWeek(),
            // end: now()->endOfYear(), ===
        )
        ->perDay()
        ->count();
 
    return [
        'datasets' => [
            [
                'label' => 'Internships per day',
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
