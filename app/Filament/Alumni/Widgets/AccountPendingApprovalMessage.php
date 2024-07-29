<?php

namespace App\Filament\Alumni\Widgets;

use Filament\Widgets\Widget;

class AccountPendingApprovalMessage extends Widget
{
    protected static string $view = 'filament.widgets.account-pending-approval-message';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return ! auth()->user()->isVerified();
    }
}
