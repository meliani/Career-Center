<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Program: string implements HasLabel
{
    case ASEDS = 'Filiere ASEDS';
    case SUD = 'Filiere SUD';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
