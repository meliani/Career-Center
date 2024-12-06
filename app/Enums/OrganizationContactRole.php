<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrganizationContactRole: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Supervisor = 'Supervisor';
    case Contact = 'Contact';
    case Parrain = 'Parrain';
    case Mentor = 'Mentor';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Mentor => 'success',
            self::Contact => 'info',
            self::Parrain => 'warning',
            self::Supervisor => 'danger'
        };
    }
}
