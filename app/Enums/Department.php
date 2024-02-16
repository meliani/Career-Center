<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Department: string implements HasLabel
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

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMO => 'Électronique, Micro-ondes et Optique',
            self::MIR => 'Mathématiques, Informatique et Réseaux',
            self::GLC => 'Gestion, Langues et Communications',
            self::SC => 'Systèmes de Communications',
            self::NULL => 'Non défini',
        };
    }
}
