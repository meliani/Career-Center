<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum DocumentTemplateType: string implements HasColor, HasIcon, HasLabel
{
    use HasBaseEnumFeatures;

    case Sheet = 'Sheet';
    case Agreement = 'Agreement';
    case AgreementMorocco = 'Agreement-Morocco';
    case AgreementFrance = 'Agreement-France';
    case AgreementOthers = 'Agreement-Others';
    case AgreementMobFrance = 'Agreement-Mob-France';
    case AgreementMobOthers = 'Agreement-Mob-Others';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Sheet => 'success',
            self::Agreement => 'danger',
            self::AgreementMorocco => 'warning',
            self::AgreementFrance => 'info',
            self::AgreementOthers => 'primary',
            self::AgreementMobFrance => 'secondary',
            self::AgreementMobOthers => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }
}
