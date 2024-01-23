<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CurrentYear: string implements HasLabel
{
    case FirstYear = 'First Year';
    case SecondYear = 'Second Year';
    case ThirdYear = 'Third Year';

    public function getLabel(): ?string
    {
        return __($this->name);

    }
}