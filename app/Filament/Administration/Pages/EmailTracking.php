<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class EmailTracking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.administration.pages.email-tracking';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
}
