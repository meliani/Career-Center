<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MidTermReportStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Submitted = 'submitted';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Submitted => __('Submitted'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Submitted => 'success',
        };
    }
}
