<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasLabel
{
    case Open = 'Open';
    case InProgress = 'InProgress';
    case Closed = 'Closed';

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public static function getArray(): array
    {
        return [
            self::Open->value,
            self::InProgress->value,
            self::Closed->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Open => 'blue',
            self::InProgress => 'yellow',
            self::Closed => 'green',
        };
    }
}
