<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Title: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Mrs = 'Mrs';
    case Mr = 'Mr';
    case Dr = 'Dr';
    // case NULL = '';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mrs => __('Mrs.'),
            self::Mr => __('Mr.'),
            self::Dr => __('Dr.'),
            // self::NULL => __(''),
        };
    }

    public function getLongTitle(): ?string
    {
        return match ($this) {
            self::Mrs => __('Madam'),
            self::Mr => __('Mister'),
            self::Dr => __('Doctor'),
            // self::NULL => __(''),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Mrs => 'warning',
            self::Dr => 'success',
            self::Mr => 'info',
            // self::NULL => 'secondary',
        };
    }
}
