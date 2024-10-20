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

    public function next(): self
    {
        $values = self::getValues();

        // Find the current key based on the value of the enum instance
        $currentKey = array_search($this->value, array_keys($values));

        if ($currentKey === false) {
            return $this;
        }

        // Go to the next array key of values
        $nextKey = $currentKey + 1;

        if (! array_key_exists($nextKey, array_keys($values))) {
            return $this;
        }

        // Get the next value
        $nextValueKey = array_keys($values)[$nextKey];
        $nextValue = $values[$nextValueKey]->value;

        return self::from($nextValue);
    }
}
