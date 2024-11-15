<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum Currency: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case MDH = 'MDH';
    case EUR = 'EUR';
    case USD = 'USD';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MDH => __('MDH'),
            self::EUR => __('EUR'),
            self::USD => __('USD'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::MDH => 'warning',
            self::EUR => 'info',
            self::USD => 'success',
        };
    }

    public function getSymbol(): ?string
    {
        return match ($this) {
            self::MDH => 'DH',
            self::EUR => '€',
            self::USD => '$',
        };
    }

    // Additional methods remain unchanged
}
