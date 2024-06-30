<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

// use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum Status: string implements HasColor, HasLabel
{
    // use IsKanbanStatus;

    case Draft = 'Draft';
    case Announced = 'Announced';
    case Rejected = 'Rejected';
    case Validated = 'Validated';
    case Completed = 'Completed';
    case Signed = 'Signed';
    case PendingCancellation = 'PendingCancellation';

    // case Approved = "Approved";
    // case Declined = "Declined";
    // case Started = "Started";
    public static function getArray(): array
    {
        return [
            self::Draft,
            self::Announced,
            self::Rejected,
            self::Validated,
            self::Completed,
            self::Signed,
            self::PendingCancellation,
            // self::Approved,
            // self::Declined,
            // self::Started,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Announced => 'info',
            self::Rejected => 'danger',
            self::Validated => 'success',
            // self::Approved => 'success',
            // self::Declined => 'danger',
            self::Signed => 'success',
            // self::Started => 'success',
            self::Completed => 'success',
            self::PendingCancellation => 'danger',
        };
    }
}
