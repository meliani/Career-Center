<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum InternshipType: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case OnSite = 'OnSite';
    case Remote = 'Remote';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::OnSite => 'success',
            self::Remote => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OnSite => 'heroicon-o-building-office-2',
            self::Remote => 'heroicon-c-computer-desktop',
        };
    }
}
