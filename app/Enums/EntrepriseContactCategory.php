<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum EntrepriseContactCategory: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Alumni = 'Alumni';
    case Supervisor = 'Supervisor';
    case DirectContact = 'DirectContact';
    case Parrain = 'Parrain';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Alumni => 'success',
            self::Supervisor => 'info',
            self::DirectContact => 'warning',
            self::Parrain => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Alumni => 'heroicon-o-user',
            self::Supervisor => 'heroicon-o-user',
            self::DirectContact => 'heroicon-o-user',
            self::Parrain => 'heroicon-o-user',
        };
    }
}
