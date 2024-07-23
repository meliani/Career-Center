<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Program: string implements HasColor, HasIcon, HasLabel
{
    case AMOA = 'AMOA';
    case ASEDS = 'ASEDS';
    case DATA = 'DATA';
    case ICCN = 'ICCN';
    case SESNUM = 'SESNUM';
    case SMARTICT = 'SMART-ICT';
    case SUD = 'SUD';
    case NULL = 'Other';

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
            self::NULL => __('Other'),
            // default => 'Other',
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
            self::NULL => __('Other'),
            // default => 'Undefined',
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
            Program::NULL->value,
        ];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AMOA => 'blue',
            self::ASEDS => 'green',
            self::DATA => 'yellow',
            self::ICCN => 'red',
            self::SESNUM => 'purple',
            self::SMARTICT => 'indigo',
            self::SUD => 'pink',
            self::NULL => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AMOA => 'heroicon-o-academic-cap',
            self::ASEDS => 'heroicon-o-academic-cap',
            self::DATA => 'heroicon-o-academic-cap',
            self::ICCN => 'heroicon-o-academic-cap',
            self::SESNUM => 'heroicon-o-academic-cap',
            self::SMARTICT => 'heroicon-o-academic-cap',
            self::SUD => 'heroicon-o-academic-cap',
            self::NULL => 'heroicon-o-academic-cap',
        };
    }
}
