<?php

namespace App\Filament\Actions\Action\Processing;

use Filament\Tables\Actions\Action;

class GoogleSheetSyncAction extends Action
{
    public static function getDefaultName(): string
    {
        return __('Google Sheet Sync');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (): void {
            $googleServices = new \App\Services\GoogleServices();
            $googleServices->importData();
            $googleServices->importProfessors();
        });

        return $static;
    }
}
