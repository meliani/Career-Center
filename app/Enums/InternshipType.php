<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Filament\Support\Contracts\HasLabel;

enum InternshipType: string implements HasColor, HasIcon, HasLabel
{
    // use IsKanbanStatus;

    case OnSite = 'OnSite';
    case Remote = 'Remote';

    public static function getArray(): array
    {
        return [
            self::OnSite,
            self::Remote,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::OnSite => 'success',
            self::Remote => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OnSite => 'heroicon-o-building-office-2',
            self::Remote => 'heroicon-c-computer-desktop',
        };
    }
}
