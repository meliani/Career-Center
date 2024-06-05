<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EntrepriseContactCategory: string implements HasColor, HasLabel
{
    case Alumni = 'Alumni';
    case Supervisor = 'Supervisor';
    case DirectContact = 'DirectContact';

    public function getLabel(): ?string
    {
        return match ($this) {
            EntrepriseContactCategory::Alumni => __(EntrepriseContactCategory::Alumni->value),
            EntrepriseContactCategory::Supervisor => __(EntrepriseContactCategory::Supervisor->value),
            EntrepriseContactCategory::DirectContact => __(EntrepriseContactCategory::DirectContact->value),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Alumni => 'success',
            self::Supervisor => 'info',
            self::DirectContact => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Alumni => 'user-tie',
            self::Supervisor => 'user-tie',
            self::DirectContact => 'user'
        };

    }
}
