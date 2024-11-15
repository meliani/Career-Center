<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Department: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case EMO = 'EMO';
    case MIR = 'MIR';
    case GLC = 'GLC';
    case SC = 'SC';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::EMO => 'Électronique, Micro-ondes et Optique',
            self::MIR => 'Mathématiques, Informatique et Réseaux',
            self::GLC => 'Gestion, Langues et Communications',
            self::SC => 'Systèmes de Communications',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::EMO => 'info',
            self::MIR => 'success',
            self::GLC => 'warning',
            self::SC => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EMO => 'heroicon-o-signal',
            self::MIR => 'heroicon-o-calculator',
            self::GLC => 'heroicon-o-language',
            self::SC => 'heroicon-o-cpu-chip',
        };
    }
}
