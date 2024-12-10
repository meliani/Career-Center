<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JuryRole: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Supervisor = 'Supervisor';
    case FirstReviewer = 'Reviewer1';
    case SecondReviewer = 'Reviewer2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Supervisor => __('Supervisor'),
            self::FirstReviewer => __('First Reviewer'),
            self::SecondReviewer => __('Second Reviewer'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Supervisor => 'success',
            self::FirstReviewer => 'info',
            self::SecondReviewer => 'info',

        };
    }
}
