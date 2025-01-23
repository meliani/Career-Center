<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExchangeType: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Inbound = 'Inbound';
    case Outbound = 'Outbound';

    public function getLabel(): string
    {
        return match ($this) {
            self::Inbound => __('Inbound Exchange'),
            self::Outbound => __('Outbound Exchange'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Inbound => 'green',
            self::Outbound => 'blue',
        };
    }
}
