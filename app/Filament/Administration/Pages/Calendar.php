<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';

    // protected static ?string $navigationGroup = 'Students and projects';

    protected static ?string $title = 'Calendrier des sorties en stage';

    /* Authorizations */
    public static function canAccess(): bool
    {
        return false;
    }

    public static function canView(): bool
    {
        return false;
    }

    public static function viewAny(): bool
    {
        return false;
    }
    /* End Authorizations */

    public static function getnavigationGroup(): string
    {
        return __('Students and projects');
    }
}
