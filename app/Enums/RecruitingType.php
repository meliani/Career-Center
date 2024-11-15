<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RecruitingType: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case SchoolManaged = 'SchoolManaged';
    case RecruiterManaged = 'RecruiterManaged';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SchoolManaged => __('School Managed'),
            self::RecruiterManaged => __('Recruiter Managed'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SchoolManaged => 'success',
            self::RecruiterManaged => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SchoolManaged => 'heroicon-o-academic-cap',
            self::RecruiterManaged => 'heroicon-o-briefcase',
        };
    }
}
