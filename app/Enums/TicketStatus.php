<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Open = 'Open';
    case InProgress = 'InProgress';
    case Closed = 'Closed';

    public function getLabel(): ?string
    {
        return __($this->name);
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
