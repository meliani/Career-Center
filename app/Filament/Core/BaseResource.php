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
        return __(static::$pluralModelLabel);
    }
    public static function viewAny(): bool
    {
        return false;
    }
    public static function view(): bool
    {
        return false;
    }
    public static function create(): bool
    {
        return false;
    }
    public static function update(): bool
    {
        return false;
    }
}
