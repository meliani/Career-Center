<?php

namespace App\Filament\Administration\Widgets;

// use Filament\Widgets\ChartWidget;
use App\Filament\Core\Widgets\FilamentChartsParentWidget as ChartWidget;
use App\Models\InternshipAgreement;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InternshipsPerWeekChart extends ChartWidget
{
    protected static ?int $sort = 10;

    protected static ?string $heading = 'Daily Announced Internships';

    protected static ?string $type = 'bar';

    public static function canView(): bool
    {
        return true;
    }

    public function getDescription(): ?string
    {
        return __('An overview of the daily announced internships.');
    }

    protected function getData(): array
    {
        $data = Trend::model(InternshipAgreement::class)
            ->between(
                // start: now()->startOfYear(),
                start: now()->subMonths(5)->startOfWeek(),
                end: now()->endOfWeek(),
                // end: now()->endOfYear(), ===
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('Internships announced count'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
}
