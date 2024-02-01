<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum Title: string implements HasLabel, HasColor
{
    case Supervisor = 'Supervisor';
    case Reviewer = 'Reviewer';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
    public function getColor(): ?string
    {
        return match ($this) {
            self::Supervisor => 'success',
            self::Reviewer => 'info',
        };
    }


}
