<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentLevel: string implements HasLabel
{
    case FirstYear = 'FirstYear';
    case SecondYear = 'SecondYear';
    case ThirdYear = 'ThirdYear';
    case AlumniTransitional = 'AlumniTransitional';
    case Alumni = 'Alumni';

    public static function getValues(): array
    {
        $reflectionClass = new \ReflectionClass(self::class);

        return $reflectionClass->getConstants();
    }

    public static function getArray(): array
    {
        return [
            StudentLevel::FirstYear->value,
            StudentLevel::SecondYear->value,
            StudentLevel::ThirdYear->value,
            StudentLevel::AlumniTransitional->value,
            StudentLevel::Alumni->value,
        ];
    }

    public function getLabel(): ?string
    {
        return __($this->value);
    }
}
