<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Program: string implements HasLabel
{
    case AMOA = 'AMOA';
    case ASEDS = 'ASEDS';
    case DATA = 'DATA';
    case ICCN = 'ICCN';
    case SESNUM = 'SESNUM';
    case SMARTICT = 'SMART-ICT';
    case SUD = 'SUD';
    case NULL = '';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AMOA => 'Innovation et AMOA',
            self::ASEDS => 'Advanced Software Engineering for Digital Services',
            self::DATA => 'Sciences de Données',
            self::ICCN => 'Cybersécurité Et Confiance Numérique',
            self::SESNUM => 'Systèmes Embraqués et Services Numériques',
            self::SMARTICT => 'Smart Information & Communication Technology Engineering',
            self::SUD => 'Systèmes Ubiquitaires et Distribués',
            self::NULL => 'N/A',
            default => 'N/A',
        };
    }

}
