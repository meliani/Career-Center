<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Filament\Support\Contracts\HasLabel;

enum OfferStatus: string implements HasColor, HasIcon, HasLabel
{
    // use IsKanbanStatus;

    case Published = 'Published';
    case Submitted = 'Submitted';
    case Disabled = 'Disabled';
    case Expired = 'Expired';

    public static function getArray(): array
    {
        return [
            self::Published,
            self::Submitted,
            self::Disabled,
            self::Expired,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
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
