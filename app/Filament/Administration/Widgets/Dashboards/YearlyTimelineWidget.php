<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Models\YearlyTimeline;
use Filament\Widgets\Widget;

class YearlyTimelineWidget extends Widget
{
    protected static string $view = 'filament.administration.widgets.yearly-timeline-widget';

    protected int | string | array $columnSpan = '1';

    public bool $showAll = false;

    public function getTimelines()
    {
        return YearlyTimeline::with('year')
            ->currentYear()
            ->orderBy('is_highlight', 'desc')
            ->orderBy('start_date')
            ->get();
    }

    public function getGroupedTimelines()
    {
        return $this->getTimelines()
            ->groupBy(function ($timeline) {
                return $timeline->start_date->format('F Y');
            });
    }

    public function loadMore()
    {
        $this->showAll = true;
    }
}
