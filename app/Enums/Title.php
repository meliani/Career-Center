<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Title: string implements HasColor, HasLabel
{
    case Mrs = 'Mrs';
    case Mr = 'Mr';
    case Dr = 'Dr';
    case NULL = '';

    public static function getArray(): array
    {
        return [
            Title::Mrs->value,
            Title::Mr->value,
            Title::Dr->value,
            Title::NULL->value,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mrs => __('Mrs.'),
            self::Mr => __('Mr.'),
            self::Dr => __('Dr.'),
            self::NULL => __(''),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Mrs => 'warning',
            self::Dr => 'success',
            self::Mr => 'info',
            self::NULL => 'secondary',
        };
    }
}
