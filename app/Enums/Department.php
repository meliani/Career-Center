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



    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMO => 'Électronique, Micro-ondes et Optique',
            self::MIR => 'Systèmes de Communications',
            self::GLC => 'Gestion, Langues et Communications',
            self::SC => 'Mathématiques, Informatique et Réseaux',
        };
    }
}
