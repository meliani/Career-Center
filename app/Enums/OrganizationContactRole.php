<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum OrganizationContactRole: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Mentor = 'Mentor';
    case Contact = 'Contact';
    case Parrain = 'Parrain';

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
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Mentor => 'user-tie',
            self::Contact => 'user',
            self::Parrain => 'user'
        };
    }
}
