<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use Carbon\Carbon;
use Filament\Widgets\Widget;

class YearTimelineWidget extends Widget
{
    protected static string $view = 'filament.administration.widgets.dashboards.year-timeline-widget';

    protected static ?int $sort = 2;

    public array $events = [];

    public function mount()
    {
        $this->loadEvents();
    }

    protected function loadEvents()
    {
        $currentYear = now()->year;

        $this->events = [
            [
                'date' => Carbon::create($currentYear, 9, 1)->format('Y-m-d'),
                'title' => __('Academic Year Start'),
                'description' => __('Welcome ceremony and orientation for new students'),
                'icon' => 'heroicon-o-academic-cap',
            ],
            [
                'date' => Carbon::create($currentYear, 10, 15)->format('Y-m-d'),
                'title' => __('First Semester Midterms'),
                'description' => __('Midterm examination period begins'),
                'icon' => 'document-text',
            ],
            [
                'date' => Carbon::create($currentYear, 12, 20)->format('Y-m-d'),
                'title' => __('Winter Break'),
                'description' => __('Holiday season and semester break'),
                'icon' => 'sparkles',
            ],
            [
                'date' => Carbon::create($currentYear + 1, 1, 15)->format('Y-m-d'),
                'title' => __('Second Semester Start'),
                'description' => __('Classes resume for the spring semester'),
                'icon' => 'academic-cap',
            ],
            [
                'date' => Carbon::create($currentYear + 1, 5, 30)->format('Y-m-d'),
                'title' => __('Final Examinations'),
                'description' => __('End of year examinations and project submissions'),
                'icon' => 'document-check',
            ],
        ];

        // Sort events by date
        usort($this->events, fn ($a, $b) => strtotime($a['date']) - strtotime($b['date']));
    }
}
