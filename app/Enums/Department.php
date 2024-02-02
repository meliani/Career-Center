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
            self::EMO => 'Département Électronique, Micro-ondes et Optique',
            self::MIR => 'Département Systèmes de Communications',
            self::GLC => 'Département Gestion, Langues et Communications',
            self::SC => 'Département Mathématiques, Informatique et Réseaux',
        };
    }
}
