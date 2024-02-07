<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Title: string implements HasColor, HasLabel
{
    case Mrs = 'Mrs';
    case Mr = 'Mr';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mrs => 'Mrs.',
            self::Mr => 'Mr.',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Mrs => 'warning',
            self::Mr => 'info',
        };
    }
}
