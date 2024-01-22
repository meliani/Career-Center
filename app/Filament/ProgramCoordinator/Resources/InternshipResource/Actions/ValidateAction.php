<?php

namespace App\Filament\Actions;

use App\Models\Internship;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class ValidateAction extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    public static function getDefaultName(): string
    {
        return __('Validate');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Internship $record): void {
            $record->withoutTimestamps(fn () => $record->validate('RIM'));
        });
        return $static;
    }
}
