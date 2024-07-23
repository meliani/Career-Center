<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WorkStatus: string implements HasColor, HasIcon, HasLabel
{
    case Employed = 'Employed';
    case Unemployed = 'Unemployed';
    case LookingForJob = 'Looking for job';
    case Student = 'Student';

    public function getLabel(): ?string
    {
        return match ($this) {
            WorkStatus::Employed => __('Employed'),
            WorkStatus::Unemployed => __('Unemployed'),
            WorkStatus::LookingForJob => __('Looking for job'),
            WorkStatus::Student => __('Student'),
        };
    }

    public static function getArray(): array
    {
        return [
            WorkStatus::Employed->value,
            WorkStatus::Unemployed->value,
            WorkStatus::LookingForJob->value,
            WorkStatus::Student->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Employed => 'success',
            self::Unemployed => 'danger',
            self::LookingForJob => 'warning',
            self::Student => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Employed => 'heroicon-o-check-circle',
            self::Unemployed => 'heroicon-o-x-circle',
            self::LookingForJob => 'heroicon-o-exclamation-circle',
            self::Student => 'heroicon-o-academic-cap',
        };
    }
}
