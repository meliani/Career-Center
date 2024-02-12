<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JuryRole: string implements HasColor, HasLabel
{
    case Supervisor = 'Supervisor';
    case Reviewer = 'Reviewer';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
    public static function getArray(): array
    {
        return [
            JuryRole::Supervisor->value,
            JuryRole::Reviewer->value,
        ];
    }
    public function getColor(): ?string
    {
        return match ($this) {
            self::Supervisor => 'success',
            self::Reviewer => 'info',
        };
    }
}
