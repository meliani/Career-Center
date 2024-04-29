<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;

class StudentAdministrationVerificationMessage extends Widget
{
    protected static string $view = 'filament.app.widgets.student-administration-verification-message';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return ! auth()->user()->is_verified;
    }
}
