<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\Timetable;

class TimetableOverviewChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 1;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'timetableOverview';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Timetable Overview';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $timetables = Timetable::with('timeslot')->get();
        $timetableDatesCount = [];

        foreach ($timetables as $timetable) {
            $date = $timetable->timeslot->start_time->toDateString();

            if (! isset($timetableDatesCount[$date])) {
                $timetableDatesCount[$date] = 0;
            }

            $timetableDatesCount[$date]++;
        }

        $timetableDatesCount = collect($timetableDatesCount);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => [
                [
                    'name' => 'Timetables',
                    'data' => $timetableDatesCount->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $timetableDatesCount->keys()->toArray(),
            ],
        ];

    }
}
