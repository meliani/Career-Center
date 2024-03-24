<?php

namespace App\Filament\Administration\Resources;

use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class EmailLogResource extends EmailResource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.administration.pages.email-tracking';

    protected static ?string $title = 'Email Logs';

    protected static ?string $navigationGroup = 'Emails';

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getTitle(): string
    {
        return __(self::$title);
    }

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator();
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return self::canAccess();
    }
}
