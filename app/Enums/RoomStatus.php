<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;


enum RoomStatus: string implements  HasLabel, HasColor
{
    case Reserved = "Reserved";
    case Occupied = "Occupied";
    case Available = "Available";

    public function getLabel(): ?string
    {
        return __($this->name);
    }
    public function getColor(): ?string {
        return match($this) {
            self::Reserved => 'warning',
            self::Occupied => 'danger',
            self::Available => 'success',
        };
    }
}