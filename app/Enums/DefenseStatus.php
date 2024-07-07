<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum DefenseStatus: string implements HasColor, HasIcon, HasLabel
{
    // use IsKanbanStatus;

    case Authorized = 'Authorized';
    case Pending = 'Pending';
    case Rejected = 'Rejected';
    case Completed = 'Completed';
    case Postponed = 'Postponed';

    public static function getArray(): array
    {
        return [
            self::Authorized,
            self::Pending,
            self::Rejected,
            self::Completed,
            self::Postponed,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Authorized => 'success',
            self::Pending => 'secondary',
            self::Rejected => 'danger',
            self::Completed => 'primary',
            self::Postponed => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Authorized => 'heroicon-o-check',
            self::Pending => 'heroicon-o-clock',
            self::Rejected => 'heroicon-o-x',
            self::Completed => 'heroicon-o-check',
            self::Postponed => 'heroicon-o-clock',
        };
    }
}
