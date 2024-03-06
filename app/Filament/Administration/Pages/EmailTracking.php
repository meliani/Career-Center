<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class EmailTracking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.administration.pages.email-tracking';

    protected static ?string $title = 'Email Tracking';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Email Tracking';

    public static function getNAvigationLabel(): string
    {
        return __(self::$navigationLabel);
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
}
