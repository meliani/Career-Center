<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;

enum CollaborationStatus: string
{
    use HasBaseEnumFeatures;

    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Accepted => __('Accepted'),
            self::Rejected => __('Rejected'),
            self::Cancelled => __('Cancelled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'danger',
        };
    }
}
