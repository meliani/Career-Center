<?php

namespace App\Filament\Alumni\Widgets;

use Filament\Widgets\Widget;

class EmailPendingVerificationMessage extends Widget
{
    protected static string $view = 'filament.widgets.email-pending-verification-message';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return ! auth()->user()->hasVerifiedEmail();
    }
}
