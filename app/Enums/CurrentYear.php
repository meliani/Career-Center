<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CurrentYear: string implements HasLabel
{
    case FirstYear = 'FirstYear';
    case SecondYear = 'SecondYear';
    case ThirdYear = 'ThirdYear';

    public function getLabel(): ?string
    {
        return __($this->name);

    }
}