<?php

namespace App\Filament\Administration\Widgets;

// use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Filament\Core\Widgets\ApexChartsParentWidget as ApexChartWidget;
use App\Models\InternshipAgreement;

class InternshipsStatusChart extends ApexChartWidget
{
    protected static ?int $sort = 1;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'internshipsStatusChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Internships advancement status';

    // protected static ?string $footer = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
    protected static ?int $contentHeight = 300; //px

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        //  will get status of internships
        $internshipData = InternshipAgreement::select('status', \DB::raw('count(`status`) as count'))
            ->groupBy('status')
            ->get()
            ->toArray();
        /* w'll use a radar chart to show the performance */
        // return [
        //     'chart' => [
        //         'type' => 'radar',
        //         'height' => 400,
        //     ],
        //     'series' => array_column($internshipData, 'count'),
        //     'labels' => array_column($internshipData, 'status'),
        //     'legend' => [
        //         'labels' => [
        //             'fontFamily' => 'inherit',
        //         ],
        //     ],
        // ];

        return [
            'chart' => [
                'type' => 'radar',
                'height' => 500,
            ],
            'series' => [
                [
                    'name' => 'InternshipsStatusChart',
                    // 'data' => array_column($internshipData, 'count'),
                    'data' => [0, 50, 100, 150],
                ],
            ],
            // 'xaxis' => [
            //     // 'min' => 0,
            //     // 'max' => 300,
            //     'categories' => array_column($internshipData, 'status'),
            //     'labels' => [
            //         'style' => [
            //             'fontFamily' => 'inherit',
            //         ],
            //     ],
            // ],
            'yaxis' => [

                'min' => 0,
                'max' => 203,
                'labels' => [
                    'show' => false,
                    // 'formatter' => [fn ($value) => $value . toFixed(2)],
                    // this will round the value to two decimal places
                    // 'formatter' => 'function (val) { return Math.floor(val); }',
                    // 'formatter' => 'function (val) { return val.toFixed(2); }',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'categories' => ['January', 'February', 'March', 'April', 'May', 'June'],
                // 'categories' => [
                //     '0',
                //     '50',
                //     '100',
                //     '150',
                //     // '200',
                // ],

            ],
            'colors' => ['#6366f1'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    // 'shade' => 'dark',
                    // 'type' => 'horizontal',
                    'shade' => 'light',
                    'type' => 'vertical',
                    // 'shadeIntensity' => 0.5,
                    'shadeIntensity' => 0.1,
                    'gradientToColors' => ['#f59e0b'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 0.6,
                ],
            ],
            'plotOptions' => [
                'radar' => [
                    'size' => 140,
                    'polygon' => [
                        // 'strokeColor' => '#e4e9f0',
                        // 'fill' => [
                        //     'colors' => ['#f8f8f8', '#fff'],
                        // ],
                        'connectorColors' => ['#ffffff', '#f1f1f1'],
                        // 'stops' => [30, 70,140,203],

                    ],
                    'dataLabels' => [
                        'show' => false,
                        'enabled' => false,
                        'background' => [
                            'enabled' => false,
                            'borderRadius' => 2,
                        ],
                        'value' => [
                            'show' => false,
                            'fontWeight' => 600,
                            'fontSize' => '24px',
                            'fontFamily' => 'inherit',
                            // 'stops' => [30,  70,140,203],
                        ],
                    ],

                ],

            ],
            'grid' => [
                'show' => false,
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }

    public function series(): array
    {
        return [
            [
                'name' => 'Series 1',
                'data' => [1, 2, 3, 4, 5], // Ensure these are integers
            ],
        ];
    }

    public static function canView(): bool
    {

        return false;
    }
}
