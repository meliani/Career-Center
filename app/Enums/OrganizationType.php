<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrganizationType: string implements HasColor, HasLabel
{
    case Company = 'Company';
    case NGO = 'NGO';
    case PublicInstitution = 'PublicInstitution';

    public function getLabel(): ?string
    {
        return match ($this) {
            OrganizationType::Company => __('Company'),
            OrganizationType::NGO => __('NGO'),
            OrganizationType::PublicInstitution => __('Public Institution'),
        };

    }

    public static function getArray(): array
    {
        return [
            OrganizationType::Company->value,
            OrganizationType::NGO->value,
            OrganizationType::PublicInstitution->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Company => 'success',
            self::NGO => 'info',
            self::PublicInstitution => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Company => 'heroicon-o-office-building',
            self::NGO => 'heroicon-o-globe',
            self::PublicInstitution => 'heroicon-o-home',
        };
    }

    public function getIconColor(): ?string
    {
        return match ($this) {
            self::Company => 'success',
            self::NGO => 'info',
            self::PublicInstitution => 'info',
        };
    }
}
