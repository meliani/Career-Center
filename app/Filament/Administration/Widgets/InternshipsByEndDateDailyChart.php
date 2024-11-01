<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class InternshipsByEndDateDailyChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 6;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'InternshipsByEndDateDailyChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Internships by end date count (daily)';

    public static function canView(): bool
    {
        return true;
    }

    protected function getFormSchema(): array
    {
        return [

            // TextInput::make('title')
            //     ->default('My Chart'),

            DatePicker::make('date_start')
                ->default('2024-07-10')
                ->live()
                ->afterStateUpdated(function () {
                    // $this->updateChartOptions();
                }),

            DatePicker::make('date_end')
                ->default('2024-07-20')
                ->live(),
            // ->afterStateUpdated(function () {
            //     $this->updateChartOptions();
            // }),

        ];
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        return $this->getData();
    }

    protected function getData(): array
    {
        // $title = $this->filterFormData['title'];
        $dateStart = new Carbon($this->filterFormData['date_start']);
        $dateEnd = new Carbon($this->filterFormData['date_end']);

        $data = Trend::query(InternshipAgreement::query())
            ->between(
                start: $dateStart,
                //
                end: $dateEnd,
            )
            ->perDay()
            ->dateColumn('ending_at')
            // ->count();
            ->aggregate('ending_at', 'count');

        return
        [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => __('Count of internships'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => $value->date),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                ],
            ],
        ];
    }
}
