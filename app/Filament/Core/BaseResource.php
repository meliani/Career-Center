<?php

namespace App\Filament\Core;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

class BaseResource extends Resource
{
    protected static ?string $modelLabel = 'INPT';

    protected static ?string $pluralModelLabel = 'INPT';

    protected static ?string $navigationGroup = 'INPT';

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getModelLabel(): string
    {
        return __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel);
    }

    public static function getNavigationGroup(): string
    {
        return __(static::$navigationGroup);
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator();
        }

        return false;
    }

    // public static function canView(Model $record): bool
    // {
    //     return false;
    // }

    // public static function canCreate(): bool
    // {
    //     return false;
    // }
    // // public static function getGlobalSearchResultTitle(Model $record): string
    // // {
    // //     return $record->name;
    // // }

    // public static function viewAny(): bool
    // {
    //     return false;
    // }

    // public static function view(): bool
    // {
    //     return false;
    // }

    // public static function create(): bool
    // {
    //     return false;
    // }

    // public static function update(): bool
    // {
    //     return false;
    // }

    // public static function delete(): bool
    // {
    //     return false;
    // }
}
