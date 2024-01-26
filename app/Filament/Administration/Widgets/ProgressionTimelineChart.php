<?php
namespace App\Filament\Administration\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Internship;

/* We want to display timeline for internships based on time of Announcement, Validation ... etc */

class ProgressionTimelineChart extends ApexChartWidget
{
    /**
        * Polling Interval
        *
        * @var string|null
        */
    protected static ?string $pollingInterval = null;

    /**
        * Chart Id
        *
        * @var string
        */
    protected static ?string $chartId = 'timeline-range-bars-basic-timeline-range-bars-chart';

    /**
        * Widget Title
        *
        * @var string|null
        */
    protected static ?string $heading = 'BasicTimelineRangeBarsChart';

    /**
        * Chart options (series, labels, types, size, animations...)
        * https://apexcharts.com/docs/options
        *
        * @return array
        */
    protected function getOptions(): array
    {
        $internshipData = Internship::select('status', \DB::raw('count(`status`) as count'))
        ->groupBy('status')
        ->get()
        ->toArray();

        return [
            'chart' => [
                'type' => 'rangeBar',
                'height' => 300,
            ],
    // TimelineRangeBarsChart display count of each status, ordred by alphabetically
    /*     case Draft = "Draft";
    case Announced = "Announced";
    case Rejected = "Rejected";
    case Validated = "Validated";
    case Approved = "Approved";
    case Declined = "Declined";
    case Signed = "Signed";
    case Started = "Started";
    case Completed = "Completed"; */
            'series' => [
                [
                    'name' => 'Draft',
                    'data' => [
                        [
                            'x' => 'Draft',
                            'y' => [0, 1]
                        ]
                    ]
                ],
                [
                    'name' => 'Announced',
                    'data' => [
                        [
                            'x' => 'Announced',
                            'y' => [1, 2]
                        ]
                    ]
                ],
                [
                    'name' => 'Rejected',
                    'data' => [
                        [
                            'x' => 'Rejected',
                            'y' => [2, 3]
                        ]
                    ]
                ],
                [
                    'name' => 'Validated',
                    'data' => [
                        [
                            'x' => 'Validated',
                            'y' => [3, 4]
                        ]
                    ]
                ],
                [
                    'name' => 'Approved',
                    'data' => [
                        [
                            'x' => 'Approved',
                            'y' => [4, 5]
                        ]
                    ]
                ],
                [
                    'name' => 'Declined',
                    'data' => [
                        [
                            'x' => 'Declined',
                            'y' => [5, 6]
                        ]
                    ]
                ],
                [
                    'name' => 'Signed',
                    'data' => [
                        [
                            'x' => 'Signed',
                            'y' => [6, 7]
                        ]
                    ]
                ],
                [
                    'name' => 'Started',
                    'data' => [
                        [
                            'x' => 'Started',
                            'y' => [7, 8]
                        ]
                    ]
                ],
                [
                    'name' => 'Completed',
                    'data' => [
                        [
                            'x' => 'Completed',
                            'y' => [8, 9]
                        ]
                    ]
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'distributed' => true,
                    'dataLabels' => [
                        'hideOverflowingLabels' => false,
                    ],
                ]
            ],
            'xaxis' => [
                'type' => 'datetime',
                'labels' => [
                    'datetimeUTC' => false,
                ],
            ],
            'yaxis' => [
                'show' => false,
            ],
            
    
        ];
    }
    public static function canView(): bool
    {

        return false;
    }
}
