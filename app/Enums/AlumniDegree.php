<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AlumniDegree: string implements HasColor, HasIcon, HasLabel
{
    case Engineer = 'Engineer';
    case Master = 'Master';
    case Bachelor = 'Bachelor';
    case Doctor = 'Doctor';

    public function getLabel(): ?string
    {
        return match ($this) {
            Degree::Engineer => __('Engineer'),
            Degree::Master => __('Master'),
            Degree::Bachelor => __('Bachelor'),
            Degree::Doctor => __('Doctor'),
        };
    }

    public static function getArray(): array
    {
        return [
            Degree::Engineer->value,
            Degree::Master->value,
            Degree::Bachelor->value,
            Degree::Doctor->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Engineer => 'success',
            self::Master => 'danger',
            self::Bachelor => 'warning',
            self::Doctor => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Engineer => 'heroicon-o-check-circle',
            self::Master => 'heroicon-o-x-circle',
            self::Bachelor => 'heroicon-o-exclamation-circle',
            self::Doctor => 'heroicon-o-academic-cap',
        };
    }
}
