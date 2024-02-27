<?php

namespace App\Filament\Actions\Action\Processing;

use App\Services\ProjectService;
use Filament\Tables\Actions\Action;

class ImportProfessorsFromInternshipAgreements extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (): void {
            ProjectService::ImportProfessorsFromInternshipAgreements();
        });

        return $static;
    }
}
