<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class DefensesCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.defenses_calendar';

    // protected static ?string $navigationGroup = 'Students and projects';

    protected static ?string $title = 'Calendrier des soutenances de PFE';

    protected static ?string $navigationLabel = 'Defenses Calendar';

    protected static ?string $navigationGroup = 'Calendars';

    /* Authorizations */
    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isSuperAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator();
    }

    public static function canView(): bool
    {
        return true;
    }

    public static function viewAny(): bool
    {
        return true;
    }
    /* End Authorizations */

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getNavigationLabel(): string
    {
        return __(self::$navigationLabel);
    }
}
