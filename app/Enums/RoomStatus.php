<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RoomStatus: string implements HasColor, HasLabel
{
    case Unavailable = 'Unavailable';
    case Available = 'Available';

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Unavailable => 'red',
            self::Available => 'green',
        };
    }
}
