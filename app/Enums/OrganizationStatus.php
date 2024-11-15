<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum OrganizationStatus: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Active = 'Active';
    case Inactive = 'Inactive';
    case Published = 'Published';

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
