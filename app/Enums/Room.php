<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Room: string implements BaseEnum, HasLabel
{
    case Amphi1 = 'Amphi 1';
    case Amphi2 = 'Amphi 2';
    case Amphi3 = 'Amphi 3';
    case Amphi4 = 'Amphi 4';

    public static function getInstances(): array
    {
        return [
            new self(Room::Amphi1),
            new self(Room::Amphi2),
            new self(Room::Amphi3),
            new self(Room::Amphi4),
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
