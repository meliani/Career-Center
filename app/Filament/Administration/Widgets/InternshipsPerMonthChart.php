<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\FilamentChartsParentWidget as ChartWidget;
use App\Models\InternshipAgreement;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InternshipsPerMonthChart extends ChartWidget
{
    protected static ?int $sort = 13;

    protected static ?string $heading = 'Announced Internships per month';

    protected static ?string $type = 'bar';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead();
    }

    public function getDescription(): ?string
    {
        return __('An overview of the announced internships per month.');
    }

    protected function getData(): array
    {
        $data = Trend::query(InternshipAgreement::query())/* ->groupBy('country') */
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->aggregate('country', 'count');

        return [
            'datasets' => [
                [
                    'label' => __('Internships announced count'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
        $data = $this->filter;
    }
}
