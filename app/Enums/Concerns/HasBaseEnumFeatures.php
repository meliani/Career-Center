<?php

namespace App\Enums\Concerns;

trait HasBaseEnumFeatures
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
     * Get array of enum labels.
     */
    public static function getLabels(): array
    {
        return array_map(fn ($case) => $case->getLabel(), static::cases());
    }

    /**
     * Get array for select dropdowns with translations.
     */
    public static function getSelectArray(): array
    {
        return array_combine(
            static::getValues(),
            array_map(fn ($case) => $case->getLabel()),
            static::cases()
        );
    }
}
