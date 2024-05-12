<?php

namespace App\Filament\Actions\Action;

use App\Models\InternshipAgreement;
use Filament\Tables\Actions\Action;

class SignAction extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    public static function getDefaultName(): string
    {
        return __('Sign');
    }

    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, InternshipAgreement $record): void {
            $record->withoutTimestamps(fn () => $record->sign());
        })
            ->requiresConfirmation(fn () => __('Are you sure you want to mark this internship as Signed?'))
            ->modalIconColor('success')
            ->modalIcon('heroicon-o-check')
            ->modalHeading(__('Sign internship agreement'))
            ->modalDescription(__('Are you sure you want to mark this internship as Signed?'))
            ->modalSubmitActionLabel(__('Mark as signed'))
            ->color('success');

        return $static;
    }
}
