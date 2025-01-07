<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, hasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Draft = 'Draft';
    case Announced = 'Announced';
    // case Rejected = 'Rejected';
    case Validated = 'Validated';
    case Completed = 'Completed';
    case Signed = 'Signed';
    case PendingCancellation = 'PendingCancellation';
    case Canceled = 'Canceled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Announced => __('Announced'),
            // self::Rejected => __('Rejected'),
            self::Validated => __('Validated'),
            self::Completed => __('Completed'),
            self::Signed => __('Signed'),
            self::PendingCancellation => __('Pending Cancellation'),
            self::Canceled => __('Canceled'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Announced => 'info',
            // self::Rejected => 'danger',
            self::Validated => 'success',
            self::Signed => 'success',
            self::Completed => 'success',
            self::PendingCancellation => 'danger',
            self::Canceled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Announced => 'heroicon-o-sparkles',
            // self::Rejected => 'heroicon-o-x-circle',
            self::Validated => 'heroicon-o-check-circle',
            self::Completed => 'heroicon-o-check-circle',
            self::Signed => 'heroicon-o-check-circle',
            self::PendingCancellation => 'heroicon-o-exclamation-triangle',
            self::Canceled => 'heroicon-o-x-circle',
        };
    }
}
