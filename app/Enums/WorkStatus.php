<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WorkStatus: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Employed = 'Employed';
    case Unemployed = 'Unemployed';
    case LookingForJob = 'Looking for job';
    case SelfEmployed = 'Self-employed';
    case Student = 'Student';
    case Retired = 'Retired';
    case Other = 'Other';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Employed => 'success',
            self::Unemployed => 'danger',
            self::LookingForJob => 'warning',
            self::SelfEmployed => 'info',
            self::Student => 'primary',
            self::Retired => 'secondary',
            self::Other => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Employed => 'heroicon-o-check-circle',
            self::Unemployed => 'heroicon-o-x-circle',
            self::LookingForJob => 'heroicon-o-x-circle',
            self::SelfEmployed => 'heroicon-o-x-circle',
            self::Student => 'heroicon-o-academic-cap',
            self::Retired => 'heroicon-o-briefcase',
            self::Other => 'heroicon-o-question-mark-circle',
        };
    }
}
