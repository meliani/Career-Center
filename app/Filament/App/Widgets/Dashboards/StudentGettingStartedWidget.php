<?php

namespace App\Filament\App\Widgets\Dashboards;

use Filament\Widgets\Widget;

class StudentGettingStartedWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.student-getting-started-widget';

    protected static ?int $sort = 1;

    // public static function canView(): bool
    // {
    //     return ! auth()->user()->is_verified;
    // }
}
