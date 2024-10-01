<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum EventParticipationStatus: string implements HasColor, HasIcon, HasLabel
{
    // cases for entreprises participatiton status

    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Cancelled = 'Cancelled';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public static function getArray(): array
    {
        return [
            self::Pending->value,
            self::Approved->value,
            self::Rejected->value,
            self::Cancelled->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Cancelled => 'heroicon-o-ban',
        };
    }
}
