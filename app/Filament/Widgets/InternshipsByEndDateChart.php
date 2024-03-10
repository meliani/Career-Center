<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InternshipsByEndDateChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 5;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsByEndDateChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Internships by end date';

    public static function canView(): bool
    {
        return true;
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // return [
        //     'chart' => [
        //         'type' => 'bar',
        //         'height' => 300,
        //     ],
        //     'series' => [
        //         [
        //             'name' => 'BasicBarChart',
        //             'data' => [7, 10, 13, 15, 18],
        //         ],
        //     ],
        //     'xaxis' => [
        //         'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        //         'labels' => [
        //             'style' => [
        //                 'fontFamily' => 'inherit',
        //             ],
        //         ],
        //     ],
        //     'yaxis' => [
        //         'labels' => [
        //             'style' => [
        //                 'fontFamily' => 'inherit',
        //             ],
        //         ],
        //     ],
        //     'colors' => ['#f59e0b'],
        //     'plotOptions' => [
        //         'bar' => [
        //             'borderRadius' => 3,
        //             'horizontal' => true,
        //         ],
        //     ],
        // ];
        return $this->getData();
    }

    protected function getData(): array
    {
        $data = Trend::query(InternshipAgreement::query())
            ->between(
                start: now()->startOfMonth(),
                //
                end: now()->addMonths(10)->endOfMonth(),
            )
            ->perMonth()
            ->dateColumn('ending_at')
            ->aggregate('ending_at', 'count');
        // dd($data);

        return
        [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Internships per month',
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
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
