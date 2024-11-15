<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum Language: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Fr = 'fr';
    case En = 'en';
    case Ar = 'ar';
    case NULL = '';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Fr => __('Français'),
            self::En => __('English'),
            self::Ar => __('العربية'),
            self::NULL => __(''),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Fr => 'warning',
            self::En => 'success',
            self::Ar => 'info',
            self::NULL => 'secondary',
        };
    }
}
