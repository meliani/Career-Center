<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum OrganizationStatus: string implements HasColor, HasLabel
{
    // this gonna gelp classifying the organizations

    case Active = 'Active';
    case Inactive = 'Inactive';
    case Published = 'Published';

    public static function getArray(): array
    {
        return [
            self::Active->value,
            self::Inactive->value,
            self::Published->value,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Published => 'info',
        };
    }
}
