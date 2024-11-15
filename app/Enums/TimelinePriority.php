<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TimelinePriority: int implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Low = 0;
    case Medium = 1;
    case High = 2;
    case Critical = 3;

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
