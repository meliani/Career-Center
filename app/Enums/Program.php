<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Program: string implements HasLabel
{
        // case AMOA = 'Ingénieur Innovation et AMOA';
    // case ASEDS = 'Advanced Software Engineering for Digital Services';
    // case DATA = 'Ingénieur des Sciences de Données';
    // case ICCN = 'Ingénieur Cybersécurité Et Confiance Numérique';
    // case SESNUM = 'Systèmes Embraqués et Services Numériques';
    // case SMARTICT = 'Ingénierie Smart  ICT« Smart Information & Communication Technology Engineering';
    // case SUD = 'Ingénierie des Systèmes Ubiquitaires et Distribués – Cloud et IoT (SUD)';
    case AMOA = 'AMOA';
    case ASEDS = 'ASEDS';
    case DATA = 'DATA';
    case ICCN = 'ICCN';
    case SESNUM = 'SESNUM';
    case SMARTICT = 'SMART-ICT';
    case SUD = 'SUD';



    public function getLabel(): ?string
    {
        return match ($this) {
            self::AMOA => 'Ingénieur Innovation et AMOA',
            self::ASEDS => 'Advanced Software Engineering for Digital Services',
            self::DATA => 'Ingénieur des Sciences de Données',
            self::ICCN => 'Ingénieur Cybersécurité Et Confiance Numérique',
            self::SESNUM => 'Systèmes Embraqués et Services Numériques',
            self::SMARTICT => 'Ingénierie Smart  ICT« Smart Information & Communication Technology Engineering',
            self::SUD => 'Ingénierie des Systèmes Ubiquitaires et Distribués – Cloud et IoT (SUD)',
        };
    }

}
