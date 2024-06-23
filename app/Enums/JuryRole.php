<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JuryRole: string implements HasColor, HasLabel
{
    case Supervisor = 'Supervisor';
    case Reviewer = 'Reviewer';
    case Reviewer1 = 'Reviewer1';
    case Reviewer2 = 'Reviewer2';

    public function getLabel(): ?string
    {
        // return __($this->name);
        return match ($this) {
            JuryRole::Supervisor => __('Supervisor'),
            JuryRole::Reviewer => __('Reviewer'),
            JuryRole::Reviewer1 => __('Reviewer 1'),
            JuryRole::Reviewer2 => __('Reviewer 2'),
        };
    }

    public static function getArray(): array
    {
        return [
            JuryRole::Supervisor->value,
            JuryRole::Reviewer->value,
            JuryRole::Reviewer1->value,
            JuryRole::Reviewer2->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Supervisor => 'success',
            self::Reviewer => 'info',
            self::Reviewer1 => 'info',
            self::Reviewer2 => 'info',

        };
    }
}
