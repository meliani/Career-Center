<?php

namespace App\Filament\Core;

use Filament\Resources\Resource;

class BaseResource extends Resource
{
    public static function getModelLabel(): string
    {
        return __(self::$modelLabel) ?? '';
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel) ?? '';
    }

    public static function viewAny(): bool
    {
        return true;
    }

    public static function view(): bool
    {
        return true;
    }

    public static function create(): bool
    {
        return true;
    }

    public static function update(): bool
    {
        return true;
    }
}
