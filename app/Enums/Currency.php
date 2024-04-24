<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Currency: string implements HasColor, HasLabel
{
    case MDH = 'MDH';
    case EUR = 'EUR';
    case USD = 'USD';

    public static function getArray(): array
    {
        return [
            Currency::MDH->value,
            Currency::EUR->value,
            Currency::USD->value,
        ];
    }

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

    public function getSymbolPosition(): ?string
    {
        return match ($this) {
            self::MDH => 'right',
            self::EUR => 'left',
            self::USD => 'left',
        };
    }

    public function getSymbolSpacing(): ?string
    {
        return match ($this) {
            self::MDH => ' ',
            self::EUR => ' ',
            self::USD => '',
        };
    }

    public function getSymbolDecimalSpacing(): ?string
    {
        return match ($this) {
            self::MDH => ',',
            self::EUR => ',',
            self::USD => '.',
        };
    }

    public function getSymbolThousandsSpacing(): ?string
    {
        return match ($this) {
            self::MDH => '.',
            self::EUR => '.',
            self::USD => ',',
        };
    }
}
