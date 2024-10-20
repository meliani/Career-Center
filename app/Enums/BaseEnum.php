<?php

namespace App\Enums;

abstract class BaseEnum implements HasLabel
{
    public static function getValues(): array
    {
        $reflectionClass = new \ReflectionClass(self::class);

        return $reflectionClass->getConstants();
    }

    public function getLabel(): ?string
    {
        return __($this->name);

    }
}
