<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentTemplateType: string implements HasColor, HasIcon, HasLabel
{
    // cases : sheet, agreement Agreement-Morocco	Agreement-France	Agreement-Others Agreement-Mob-France Agreement-Mob-Others
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

    public static function getArray(): array
    {
        return [
            self::Sheet->value,
            self::Agreement->value,
            self::AgreementMorocco->value,
            self::AgreementFrance->value,
            self::AgreementOthers->value,
            self::AgreementMobFrance->value,
            self::AgreementMobOthers->value,
        ];
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
        return match ($this) {
            self::Sheet => 'heroicon-o-academic-cap',
            self::Agreement => 'heroicon-o-academic-cap',
            self::AgreementMorocco => 'heroicon-o-academic-cap',
            self::AgreementFrance => 'heroicon-o-academic-cap',
            self::AgreementOthers => 'heroicon-o-academic-cap',
            self::AgreementMobFrance => 'heroicon-o-academic-cap',
            self::AgreementMobOthers => 'heroicon-o-academic-cap',
        };
    }
}
