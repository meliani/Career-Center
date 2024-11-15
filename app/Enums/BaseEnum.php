<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

abstract class BaseEnum implements HasLabel, HasColor, HasIcon
{
    /**
     * Get all enum cases.
     */
    public static function getArray(): array
    {
        return static::cases();
    }

    /**
     * Get array of enum values.
     */
    public static function getValues(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Get array for select dropdowns.
     */
    public static function getSelectArray(): array
    {
        return array_combine(
            static::getValues(),
            array_map(fn($case) => $case->getLabel(), static::cases())
        );
    }

    /**
     * Get array of labels.
     */
    public static function getLabels(): array
    {
        return array_map(fn($case) => $case->getLabel(), static::cases());
    }

    abstract public function getLabel(): ?string;
    abstract public function getColor(): ?string;
    abstract public function getIcon(): ?string;
}
