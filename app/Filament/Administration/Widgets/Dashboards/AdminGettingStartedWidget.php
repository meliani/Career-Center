<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use Filament\Widgets\Widget;

class AdminGettingStartedWidget extends Widget
{
    protected static string $view = 'filament.administration.widgets.dashboards.admin-getting-started-widget';

    protected static ?int $sort = 1;

    // public static function canView(): bool
    // {
    //     return ! auth()->user()->is_verified;
    // }
}
