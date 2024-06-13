<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Language: string implements HasColor, HasLabel
{
    case Fr = 'fr';
    case En = 'en';
    case Ar = 'ar';
    case NULL = '';

    public static function getArray(): array
    {
        return [
            self::Fr->value,
            self::En->value,
            self::Ar->value,
            self::NULL->value,
        ];
    }

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
