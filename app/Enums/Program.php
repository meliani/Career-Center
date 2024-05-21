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
            self::AMOA => 'AMOA',
            self::ASEDS => 'ASEDS',
            self::DATA => 'DATA',
            self::ICCN => 'ICCN',
            self::SESNUM => 'SESNUM',
            self::SMARTICT => 'SMART-ICT',
            self::SUD => 'SUD',
            self::NULL => __('Undefined'),
            default => 'Undefined',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::AMOA => 'Innovation et AMOA',
            self::ASEDS => 'Ingénierie Logicielle avancée pour les Services Numériques',
            self::DATA => 'Sciences de Données',
            self::ICCN => 'Cybersécurité & Confiance Numérique',
            self::SESNUM => 'Systèmes Embraqués et Services Numériques',
            self::SMARTICT => "Ingénierie des Technologies de l'Information et de la Communication Intelligentes",
            self::SUD => 'Ingénierie des Systèmes Ubiquitaires et Distribués',
            self::NULL => __('Undefined'),
            default => 'Undefined',
        };
    }

    public static function getArray(): array
    {
        return [
            Program::AMOA->value,
            Program::ASEDS->value,
            Program::DATA->value,
            Program::ICCN->value,
            Program::SESNUM->value,
            Program::SMARTICT->value,
            Program::SUD->value,
        ];
    }
}
