<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentLevel: string implements HasLabel
{
    case FirstYear = 'FirstYear';
    case SecondYear = 'SecondYear';
    case ThirdYear = 'ThirdYear';

    public static function getValues(): array
    {
        $reflectionClass = new \ReflectionClass(self::class);

        return $reflectionClass->getConstants();
    }

    public static function getArray(): array
    {
        return [
            'FirstYear' => 'First Year',
            'SecondYear' => 'Second Year',
            'ThirdYear' => 'Third Year',
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->name);

    }
}
