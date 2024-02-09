<?php

namespace App\Filament\Actions;

use App\Models\InternshipAgreement;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class ReceiveAction extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    public static function getDefaultName(): string
    {
        return __('Internship Agreement received');
    }

    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, InternshipAgreement $record): void {
            $record->withoutTimestamps(fn () => $record->receive());
        });

        return $static;
    }
}
