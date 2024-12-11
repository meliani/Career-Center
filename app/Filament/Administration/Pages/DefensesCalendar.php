<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class DefensesCalendar extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.defenses_calendar';

    // protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?string $title = 'Calendrier des soutenances de PFE';

    protected static ?string $navigationLabel = 'Defenses Calendar';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?int $navigationSort = 5;

    /* Authorizations */
    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isSuperAdministrator();
        // || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();

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
        return __(self::$navigationGroup);
    }

    public static function getNavigationLabel(): string
    {
        return __(self::$navigationLabel);
    }
}
