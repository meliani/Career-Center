<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Filament\Support\Contracts\HasLabel;

enum InternshipLevel: string implements HasColor, HasLabel
{
    // use IsKanbanStatus;

    case FinalYearInternship = 'FinalYearInternship';
    case IntroductoryInternship = 'IntroductoryInternship';
    case TechnicalInternship = 'TechnicalInternship';
    // case MasterThesis = 'MasterThesis';
    // case PhDThesis = 'PhDThesis';

    public static function getArray(): array
    {
        return [
            self::FinalYearInternship,
            self::IntroductoryInternship,
            self::TechnicalInternship,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
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
            self::FinalYearInternship => 'heroicon-o-graduation-cap',
            self::IntroductoryInternship => 'heroicon-o-cog',
            self::TechnicalInternship => 'heroicon-o-cog',
        };
    }
}
