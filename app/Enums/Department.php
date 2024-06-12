<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Department: string implements HasColor, HasIcon, HasLabel
{
    /* MIR,     Département Systèmes de Communications.
    Département Eélectronique , Micro ondes et Optique.
    Département Mathématiques, Informatique et Réseaux.
    Département Gestion, Langues et Communications. */
    case EMO = 'EMO';
    case MIR = 'MIR';
    case GLC = 'GLC';
    case SC = 'SC';
    case NULL = '';

    public static function toArray(): array
    {
        return [
            Department::EMO->value,
            Department::MIR->value,
            Department::GLC->value,
            Department::SC->value,
            Department::NULL->value,

        ];
    }

    public static function getArray(): array
    {
        return [
            Department::EMO->value,
            Department::MIR->value,
            Department::GLC->value,
            Department::SC->value,
            Department::NULL->value,

        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMO => 'EMO',
            self::MIR => 'MIR',
            self::GLC => 'GLC',
            self::SC => 'SC',
            self::NULL => __('Undefined'),
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::EMO => 'Électronique, Micro-ondes et Optique',
            self::MIR => 'Mathématiques, Informatique et Réseaux',
            self::GLC => 'Gestion, Langues et Communications',
            self::SC => 'Systèmes de Communications',
            self::NULL => __('Undefined'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::EMO => 'info',
            self::MIR => 'success',
            self::GLC => 'warning',
            self::SC => 'danger',
            self::NULL => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EMO => 'heroicon-o-signal',
            self::MIR => 'heroicon-o-calculator',
            self::GLC => 'heroicon-o-language',
            self::SC => 'heroicon-o-cpu-chip',
            self::NULL => 'heroicon-o-no-symbol',
        };
    }
}
