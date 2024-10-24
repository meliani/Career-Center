<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Filament\Support\Contracts\HasLabel;

enum RecrutingType: string implements HasColor, HasIcon, HasLabel
{
    // use IsKanbanStatus;

    case SchoolManaged = 'SchoolManaged';
    case RecruiterManaged = 'RecruiterManaged';

    public static function getArray(): array
    {
        return [
            self::SchoolManaged,
            self::RecruiterManaged,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SchoolManaged => 'success',
            self::RecruiterManaged => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SchoolManaged => 'heroicon-o-academic-cap',
            self::RecruiterManaged => 'heroicon-o-briefcase',
        };
    }
}
