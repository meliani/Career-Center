<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;

class StudentEmailVerificationMessage extends Widget
{
    protected static string $view = 'filament.app.widgets.student-email-verification-message';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return ! auth()->user()->hasVerifiedEmail();
    }
}
