<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TicketClosedReason: string implements HasColor, HasLabel
{
    case Resolved = 'Resolved';
    case Duplicate = 'Duplicate';
    case Invalid = 'Invalid';
    case Unrelated = 'Unrelated';
    case unresolved = 'Unresolved';
    case NULL = '';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Resolved => __('Resolved'),
            self::Duplicate => __('Duplicate'),
            self::Invalid => __('Invalid'),
            self::Unrelated => __('Unrelated'),
            self::unresolved => __('Unresolved'),
            self::NULL => __('Undefined'),
        };
    }

    public static function getArray(): array
    {
        return [
            TicketClosedReason::Resolved->value,
            TicketClosedReason::Duplicate->value,
            TicketClosedReason::Invalid->value,
            TicketClosedReason::Unrelated->value,
            TicketClosedReason::unresolved->value,
            TicketClosedReason::NULL->value,
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Resolved => 'success',
            self::Duplicate => 'info',
            self::Invalid => 'danger',
            self::Unrelated => 'warning',
            self::unresolved => 'secondary',
            self::NULL => 'secondary',
        };
    }
}
