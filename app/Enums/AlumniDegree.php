<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum AlumniDegree: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Engineer = 'Engineer';
    case Master = 'Master';
    case Doctor = 'Doctor';

    public function getLabel(): ?string
    {
        return __($this->name);
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
        return 'heroicon-o-academic-cap';
    }
}
