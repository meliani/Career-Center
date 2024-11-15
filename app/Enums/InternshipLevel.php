<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum InternshipLevel: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case FinalYearInternship = 'FinalYearInternship';
    case TechnicalInternship = 'TechnicalInternship';
    case IntroductoryInternship = 'IntroductoryInternship';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::FinalYearInternship => 'success',
            self::IntroductoryInternship => 'info',
            self::TechnicalInternship => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::FinalYearInternship => 'heroicon-o-academic-cap',
            self::IntroductoryInternship => 'heroicon-o-cog',
            self::TechnicalInternship => 'heroicon-o-cog',
        };
    }
}
