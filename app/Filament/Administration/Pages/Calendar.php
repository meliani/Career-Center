<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';

    protected static ?string $navigationGroup = 'Internships';

    protected static ?string $title = 'Calendrier des sorties en stage';

    public static function canView(): bool
    {
        return false;
    }
    public static function viewAny(): bool
    {
        return false;
    }
    // public static function canAccess(): bool
    // {
    //     return false;
    // }
    
}
