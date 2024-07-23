<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AlumniDegree: string implements HasColor, HasIcon, HasLabel
{
    case Engineer = 'Engineer';
    // case Master = 'Master';
    // case Doctor = 'Doctor';

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public static function getArray(): array
    {
        return [
            self::Engineer->value,
            self::Master->value,
            self::Doctor->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Engineer => 'success',
            self::Master => 'danger',
            self::Doctor => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Engineer => 'heroicon-o-academic-cap',
            self::Master => 'heroicon-o-academic-cap',
            self::Doctor => 'heroicon-o-academic-cap',
        };
    }
}
