<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TimelineStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function getArray(): array
    {
        return [
            self::Pending,
            self::InProgress,
            self::Completed,
            self::Cancelled,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::InProgress => __('In Progress'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'info',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}