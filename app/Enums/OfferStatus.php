<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum OfferStatus: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Published = 'Published';
    case Submitted = 'Submitted';
    case Disabled = 'Disabled';
    case Expired = 'Expired';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Published => 'success',
            self::Submitted => 'info',
            self::Disabled => 'danger',
            self::Expired => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Published => 'heroicon-o-check',
            self::Submitted => 'heroicon-o-check',
            self::Disabled => 'heroicon-o-mark',
            self::Expired => 'heroicon-o-mark',
        };
    }
}
