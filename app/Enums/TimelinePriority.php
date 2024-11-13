<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TimelinePriority: int implements HasColor, HasLabel
{
    case Low = 0;
    case Medium = 1;
    case High = 2;
    case Critical = 3;

    public static function getArray(): array
    {
        return [
            self::Low,
            self::Medium,
            self::High,
            self::Critical,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Medium => __('Medium'),
            self::High => __('High'),
            self::Critical => __('Critical'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'success',
            self::High => 'warning',
            self::Critical => 'danger',
        };
    }
}
