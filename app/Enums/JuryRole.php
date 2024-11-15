<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum JuryRole: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Supervisor = 'Supervisor';
    case Reviewer = 'Reviewer';
    case Reviewer1 = 'Reviewer1';
    case Reviewer2 = 'Reviewer2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Supervisor => __('Supervisor'),
            self::Reviewer => __('Reviewer'),
            self::Reviewer1 => __('Reviewer 1'),
            self::Reviewer2 => __('Reviewer 2'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Supervisor => 'success',
            self::Reviewer => 'info',
            self::Reviewer1 => 'info',
            self::Reviewer2 => 'info',

        };
    }
}
