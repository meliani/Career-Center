<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrganizationContactRole: string implements HasColor, HasLabel
{
    case Mentor = 'Mentor';
    case Contact = 'Contact';
    case Parrain = 'Parrain';

    public function getLabel(): ?string
    {
        return match ($this) {
            OrganizationContactRole::Mentor => __(OrganizationContactRole::Mentor->value),
            OrganizationContactRole::Contact => __(OrganizationContactRole::Contact->value),
            OrganizationContactRole::Parrain => __(OrganizationContactRole::Parrain->value),
        };
    }

    public static function getArray(): array
    {
        return [
            OrganizationContactRole::Mentor->value,
            OrganizationContactRole::Contact->value,
            OrganizationContactRole::Parrain->value,
        ];
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
